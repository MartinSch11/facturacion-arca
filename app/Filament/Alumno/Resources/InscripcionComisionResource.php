<?php

namespace App\Filament\Alumno\Resources;

use App\Filament\Alumno\Resources\InscripcionComisionResource\Pages;
use App\Filament\Alumno\Resources\InscripcionComisionResource\RelationManagers;
use App\Models\Alumnos;
use App\Models\ComisionMateria;
use App\Models\Configuracion;
use App\Models\Cursada;
use App\Models\InscripcionComision;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InscripcionComisionResource extends Resource
{
    protected static ?string $model = ComisionMateria::class;
    protected static ?string $navigationLabel = 'Inscripción a Comisiones';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public ?Alumnos $alumno = null;
    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return Configuracion::inscripcionHabilitada();
    }

    public static function getPanel(): string
    {
        return 'alumno';
    }

    public static function getModelLabel(): string
    {
        return 'Inscripción a Comisión';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Inscripción a Comisiones';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $alumno = Auth::user()?->alumno;
                if (!$alumno) {
                    $query->whereRaw('1=0');
                    return;
                }

                $idCarrera = $alumno->carreraXAlumno?->id_carrera;
                $config = Configuracion::first();
                $periodo = $config?->periodoActual();

                if (!$periodo) {
                    $query->whereRaw('1=0');
                    return;
                }

                $materiasAprobadasIds = $alumno->materiasAprobadas()->pluck('id_materia')->toArray();

                // Obtener todas las comisiones posibles
                $comisiones = ComisionMateria::whereHas('materia', function ($q) use ($idCarrera, $periodo) {
                        $q->whereHas('planEstudio', function ($q2) use ($idCarrera) {
                            $q2->where('id_carrera', $idCarrera);
                        })
                        ->where(function ($q3) use ($periodo) {
                            $q3->where('modalidad', $periodo)
                               ->orWhere('modalidad', 'Anual');
                        });
                    })
                    ->get();

                // Filtrar correlatividades en PHP y obtener solo los IDs válidos
                $idsValidos = $comisiones->filter(function ($comision) use ($materiasAprobadasIds, $periodo) {
                    $materia = $comision->materia;

                    if ($materia->modalidad !== $periodo && $materia->modalidad !== 'Anual') {
                        return false;
                    }

                    $correlativas = $materia->correlativas()->pluck('id_correlativa')->toArray();

                    if (empty($materiasAprobadasIds) && empty($correlativas)) {
                        return true;
                    }

                    if (empty($correlativas)) {
                        return true;
                    }

                    foreach ($correlativas as $id) {
                        if (!in_array($id, $materiasAprobadasIds)) {
                            return false;
                        }
                    }

                    return true;
                })->pluck('id')->toArray();

                // Ahora sí, filtra el query final solo con los IDs válidos
                $query->whereIn('id', $idsValidos);
            })
            ->columns([
                //
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Comisión')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('materia.anio')
                    ->label('Año')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cupo')
                    ->label('Cupo')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('inscribir')
                    ->disabled(function (ComisionMateria $record) {
                        $alumno = Auth::user()?->alumno;
                        // Deshabilitar si ya está inscripto o no hay cupo
                        $yaInscripto = $alumno
                            ? $alumno->cursadas()
                                ->where('id_materia', $record->id_materia)
                                ->where('estado', 'Cursando')
                                ->exists()
                            : true;
                        return $yaInscripto || $record->cupo <= 0;
                    })
                    ->action(function (ComisionMateria $record) {
                        $alumno = Auth::user()?->alumno;

                        // Crear la cursada
                        // Obtener el cuatrimestre actual según la fecha
                        $mes = Carbon::now()->month;
                        $cuatrimestre = ($mes >= 1 && $mes <= 7) ? '1C' : '2C';

                        Cursada::create([
                            'dni_alumno' => $alumno->dni,
                            'id_materia' => $record->id_materia,
                            'id_comision_materia' => $record->id,
                            'anio' => Carbon::now()->year,
                            'cuatrimestre' => $cuatrimestre,
                            'estado' => 'Cursando',
                            'fecha_inscripcion' => Carbon::now(),
                        ]);

                        // Bajar el cupo en 1
                        $record->decrement('cupo');

                        Notification::make()
                            ->title('Inscripción exitosa')
                            ->body('Te has inscripto a la comisión de ' . $record->materia->nombre)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->label('Inscribirse')
                    ->icon('heroicon-o-plus'),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInscripcionComisions::route('/'),
        ];
    }
}
