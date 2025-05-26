<?php

namespace App\Filament\Pages\Auth;

use App\Models\Alumnos;
use App\Models\CarreraXAlumno;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
class Login extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    protected static string $view = 'filament-panels::pages.auth.login';

    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            $user = Filament::auth()->user();
            if ($user instanceof User && isset($user->role) && $user->role === 'admin') {
                redirect()->intended('/admin');
            } elseif ($user instanceof User && isset($user->role) && $user->role === 'alumno') {
                redirect()->intended('/alumno');
            } elseif ($user instanceof User && isset($user->role) && $user->role === 'profesor') {
                redirect()->intended('/profesor');
            } else {
                redirect()->intended(Filament::getUrl());
            }
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        // Depuración: Confirmar que el método se ejecuta
        Log::info('Inicio del método authenticate');

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        // Depuración: Registrar datos del formulario
        Log::info('Datos del formulario', [
            'form_data' => $data,
        ]);

        $credentials = $this->getCredentialsFromFormData($data);

        // Depuración: Registrar credenciales
        Log::info('Credenciales enviadas', [
            'credentials' => array_keys($credentials), // Evitar registrar la contraseña
        ]);

        if (!Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
            Log::info('Fallo en la autenticación');
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // Depuración: Registrar datos del usuario
        Log::info('Usuario autenticado', [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'user_name' => $user->name ?? null,
            'user_role' => $user instanceof User && isset($user->role) ? $user->role : null,
            'user_dni' => $user instanceof User && isset($user->dni) ? $user->dni : null,
        ]);

        if (!($user instanceof FilamentUser)) {
            Log::info('Usuario no es FilamentUser');
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        if (!$user->canAccessPanel(Filament::getCurrentPanel())) {
            Log::info('Usuario no tiene acceso al panel', [
                'panel_id' => Filament::getCurrentPanel()->getId(),
            ]);
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        // Establecer datos del alumno en la sesión para alumnos
        if ($user instanceof User && isset($user->role) && $user->role === 'alumno' && isset($user->dni)) {
            // Obtener datos de la tabla alumnos
            $alumno = Alumnos::where('dni', $user->dni)->first();

            // Depuración: Registrar datos de alumno
            Log::info('Consulta a tabla alumnos', [
                'dni_buscado' => $user->dni,
                'alumno_encontrado' => $alumno ? [
                    'dni' => $alumno->dni,
                    'nombre' => $alumno->nombre,
                    'apellido' => $alumno->apellido,
                ] : null,
            ]);

            // Obtener matrícula de carreraxalumno
            $matricula = CarreraXAlumno::where('dni_alumno', $user->dni)->first();

            // Depuración: Registrar datos de matrícula
            Log::info('Consulta a tabla carrerax1alumno', [
                'dni_buscado' => $user->dni,
                'matricula_encontrada' => $matricula ? [
                    'matricula' => $matricula->matricula,
                    'dni_alumno' => $matricula->dni_alumno,
                    'id_carrera' => $matricula->id_carrera,
                ] : null,
            ]);

            if ($alumno && $matricula) {
                $alumnoData = [
                    'dni' => $user->dni,
                    'nombre' => $alumno->nombre,
                    'apellido' => $alumno->apellido,
                    'matricula' => $matricula->matricula,
                ];
                Session::put('alumno_data', $alumnoData);

                // Depuración: Registrar datos guardados en la sesión
                Log::info('Datos guardados en sesión', [
                    'alumno_data' => $alumnoData,
                ]);

                // Notificación: Confirmar datos guardados
                Notification::make()
                    ->title('Datos de alumno guardados')
                    ->body(json_encode($alumnoData, JSON_PRETTY_PRINT))
                    ->success()
                    ->send();
            } else {
                // Depuración: Registrar por qué no se guardaron los datos
                Log::info('No se guardaron datos en sesión', [
                    'alumno_existe' => !is_null($alumno),
                    'matricula_existe' => !is_null($matricula),
                ]);

                // Notificación: Mostrar por qué falló
                Notification::make()
                    ->title('Error al guardar datos de alumno')
                    ->body('Alumno: ' . ($alumno ? 'Encontrado' : 'No encontrado') . ' | Matrícula: ' . ($matricula ? 'Encontrada' : 'No encontrada'))
                    ->danger()
                    ->send();
            }
        } else {
            // Depuración: Registrar por qué no se procesó el alumno
            Log::info('No se procesaron datos de alumno', [
                'is_user_instance' => $user instanceof User,
                'role_defined' => isset($user->role),
                'role_value' => isset($user->role) ? $user->role : null,
                'dni_defined' => isset($user->dni),
            ]);

            // Notificación: Mostrar por qué no se procesó
            Notification::make()
                ->title('No se procesaron datos de alumno')
                ->body(json_encode([
                    'is_user_instance' => $user instanceof User,
                    'role_defined' => isset($user->role),
                    'role_value' => isset($user->role) ? $user->role : null,
                    'dni_defined' => isset($user->dni),
                ], JSON_PRETTY_PRINT))
                ->warning()
                ->send();
        }

        session()->regenerate();

        // Depuración: Registrar sesión final
        Log::info('Sesión después de regenerar', [
            'session_alumno_data' => session('alumno_data'),
        ]);

        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        $this->getRolesFormComponent(),
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->extraAttributes([
                        'style' => 'justify-center items-center'
                    ]),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Usuario')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getRolesFormComponent(): Component
    {
        return ToggleButtons::make('role')
            ->label('')
            ->options([
                'alumno' => 'Alumno',
                'profesor' => 'Profesor',
                'admin' => 'Administrador',
            ])
            ->icons([
                'alumno' => 'heroicon-s-academic-cap',
                'profesor' => 'heroicon-s-user-group',
                'admin' => 'heroicon-s-check-circle',
            ])
            ->default('alumno')
            ->columns(3);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'));
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return "Acceso al Sistema";
    }

    public function getHeading(): string | Htmlable
    {
        return "Iniciar Sesión";
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('authenticate')
                ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
                ->submit('authenticate'),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }
}
