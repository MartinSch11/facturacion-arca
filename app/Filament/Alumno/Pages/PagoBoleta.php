<?php

namespace App\Filament\Alumno\Pages;

use App\Models\Boleta;
use App\Services\PagoBoletaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;

class PagoBoleta extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithFormActions;
    use Forms\Concerns\InteractsWithForms;

    public ?Boleta $boleta = null;

    public static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $view = 'filament.alumno.pages.pago-boleta';
    protected static ?string $slug = 'pago-boleta';
    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPanel(): string
    {
        return 'alumno';
    }

    public function mount(): void
    {
        $boletaId = request()->get('boleta');

        if (!is_numeric($boletaId)) {
            abort(400, 'Boleta ID inválido');
        }

        $this->boleta = Boleta::query()->where('nro_boleta', $boletaId)->firstOrFail();

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('forma_pago')
                    ->label('Forma de Pago')
                    ->options([
                        'contado' => 'Contado',
                        'tarjeta_credito' => 'Tarjeta de Crédito',
                        'tarjeta_debito' => 'Tarjeta de Débito',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('tipo_factura')
                    ->label('Tipo de Factura')
                    ->options([
                        'A' => 'Factura A',
                        'B' => 'Factura B',
                        'C' => 'Factura C',
                    ])
                    ->default('C')
                    ->required()
                    ->native(false),
            ])
            ->statePath('data');
    }

    public function pagar(): void
    {
        try {
            $formData = $this->form->getState();

            $service = app(PagoBoletaService::class);
            $service->procesarPago($this->boleta, $formData['forma_pago'], $formData['tipo_factura'] ?? 'C');

            Notification::make()
                ->title('Pago completado.')
                ->success()
                ->send();

            $this->redirect('/alumno/boletas-alumnos');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en el proceso de pago')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

