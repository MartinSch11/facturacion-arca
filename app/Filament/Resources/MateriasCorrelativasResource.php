<?php

namespace App\Filament\Resources;

use App\Filament\Pages\EditarCorrelativasMateria;
use App\Filament\Resources\MateriasCorrelativasResource\Pages;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\MateriasCorrelativas;
use App\Models\PlanEstudio;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class MateriasCorrelativasResource extends Resource
{
    protected static ?string $model = MateriasCorrelativas::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Correlatividades';

    public static function getNavigationItem(): NavigationItem
    {
        return NavigationItem::make(static::getNavigationLabel())
            ->url(static::getUrl())
            ->icon(static::getNavigationIcon())
            ->group(static::getNavigationGroup())
            ->sort(static::getNavigationSort());
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('plan_estudio_id')
                ->label('Plan de Estudio')
                ->options(PlanEstudio::all()->pluck('nombre', 'id'))
                ->reactive()
                ->required(),

            Forms\Components\Select::make('id_materia')
                ->label('Materia')
                ->options(
                    fn(callable $get) =>
                    Materia::where('id_plan_estudio', $get('plan_estudio_id'))
                        ->pluck('nombre', 'id')
                )
                ->reactive()
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('id_correlativas')
                ->label('Correlativas')
                ->multiple()
                ->options(function (callable $get): Collection {
                    $materiaId = $get('id_materia');
                    $planId = $get('plan_estudio_id');

                    $anioMateria = Materia::find($materiaId)?->anio;

                    return Materia::where('id_plan_estudio', $planId)
                        ->where('id', '!=', $materiaId)
                        ->when($anioMateria, fn($query) => $query->where('anio', '<=', $anioMateria))
                        ->orderBy('anio')    
                        ->orderBy('nombre')
                        ->pluck('nombre', 'id');
                })
                ->required()
                ->searchable()
                ->preload()
                ->helperText('Seleccioná una o más materias que deben estar aprobadas como requisito.'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('materia.nombre')->label('Materia'),
                Tables\Columns\TextColumn::make('correlativa.nombre')->label('Correlativa'),
                Tables\Columns\TextColumn::make('correlativa.planEstudio.carrera.nombre')
                    ->label('Carrera de la Correlativa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correlativa.anio')
                    ->label('Año de la materia')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_materia')
                    ->label('Materia')
                    ->relationship('materia', 'nombre'),
                Tables\Filters\SelectFilter::make('id_correlativa')
                    ->label('Correlativa')
                    ->relationship('correlativa', 'nombre'),
                Tables\Filters\SelectFilter::make('carrera')
                    ->label('Carrera')
                    ->options(
                        fn() =>
                        Carrera::whereHas('planesEstudio.materias.correlativas')
                            ->pluck('nombre', 'id_carrera')
                    )
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            return $query->whereHas('materia.planEstudio', fn($q) => $q->where('id_carrera', $data['value']));
                        }
                        return $query;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('editarCorrelativas')
                        ->label('Editar')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn($record) => EditarCorrelativasMateria::getUrl(['materia' => $record->id_materia])),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMateriasCorrelativas::route('/'),
            'create' => Pages\CreateMateriasCorrelativas::route('/create'),
        ];
    }
}
