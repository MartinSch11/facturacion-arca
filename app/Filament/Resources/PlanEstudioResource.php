<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanEstudioResource\Pages;
use App\Models\Materia;
use App\Models\MateriasCorrelativas;
use App\Models\PlanEstudio;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlanEstudioResource extends Resource
{
    protected static ?string $model = PlanEstudio::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Planes de Estudio';

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
            Forms\Components\TextInput::make('nombre')->required(),
            Forms\Components\Select::make('id_carrera')
                ->relationship('carrera', 'nombre')
                ->required(),
            Forms\Components\TextInput::make('anio_implementacion')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('carrera.nombre')->label('Carrera'),
                Tables\Columns\TextColumn::make('anio_implementacion')->label('Año'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanesEstudio::route('/'),
            'create' => Pages\CreatePlanEstudio::route('/create'),
            'edit' => Pages\EditPlanEstudio::route('/{record}/edit'),
            'view' => Pages\ViewPlanEstudio::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\PlanEstudioResource\RelationManagers\MateriasRelationManager::class,
            \App\Filament\Resources\PlanEstudioResource\RelationManagers\MateriasCorrelativasRelationManager::class,
        ];
    }
}
