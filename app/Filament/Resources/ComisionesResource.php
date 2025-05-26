<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComisionesResource\Pages;
use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use App\Models\Profesor;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class ComisionesResource extends Resource
{
    protected static ?string $model = ComisionMateria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getSlug(): string
    {
        return 'comisiones';
    }

    public static function getModelLabel(): string
    {
        return 'Comisión';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Comisiones';
    }

    public static function getNavigationItem(): NavigationItem
    {
        return NavigationItem::make(static::getNavigationLabel())
            ->url(static::getUrl())
            ->icon(static::getNavigationIcon())
            ->group(static::getNavigationGroup())
            ->sort(static::getNavigationSort());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre de la Comisión')
                    ->required()
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->afterStateUpdated(fn($state, callable $set) => $set('nombre', mb_strtoupper($state))),
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
                    ->required()
                    ->searchable()
                    ->reactive(),
                Forms\Components\Select::make('profesor_id')
                    ->label('Profesor')
                    ->options(
                        Profesor::all()->mapWithKeys(function ($profesor) {
                            return [$profesor->getKey() => $profesor->nombre_completo];
                        })->toArray()
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('turno')
                    ->label('Turno')
                    ->options([
                        'M' => 'Mañana',
                        'T' => 'Tarde',
                        'N' => 'Noche',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('division')
                    ->label('División')
                    ->required()
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->afterStateUpdated(fn($state, callable $set) => $set('nombre', mb_strtoupper($state))),
                Forms\Components\TextInput::make('cupo')->label('Cupo de Alumnos')->numeric()->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Comisión'),
                Tables\Columns\TextColumn::make('materia.nombre')->label('Materia')->searchable(),
                Tables\Columns\TextColumn::make('profesor.nombre_completo')->label('Profesor'),
                Tables\Columns\TextColumn::make('turno'),
                Tables\Columns\TextColumn::make('materia.modalidad')->label('Cuatrimestre'),
                Tables\Columns\TextColumn::make('division'),
                Tables\Columns\TextColumn::make('cupo'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComisiones::route('/'),
            'create' => Pages\CreateComisiones::route('/create'),
            'edit' => Pages\EditComisiones::route('/{record}/edit'),
        ];
    }
}
