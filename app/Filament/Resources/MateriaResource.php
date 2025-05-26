<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriaResource\Pages;
use App\Models\Materia;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class MateriaResource extends Resource
{
    protected static ?string $model = Materia::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Materias';

    public static function getNavigationItem(): NavigationItem
    {
        return NavigationItem::make(static::getNavigationLabel())
            ->url(static::getUrl())
            //->icon(static::getNavigationIcon())
            ->group(static::getNavigationGroup())
            ->sort(static::getNavigationSort());
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')->required(),
            Forms\Components\Select::make('id_plan_estudio')
                ->relationship('planEstudio', 'nombre')
                ->required()
                ->label('Plan de Estudio'),
            Forms\Components\TextInput::make('anio')->numeric()->required()->label('Año de la carrera')->maxValue(5),
            Forms\Components\Select::make('modalidad')
                ->options([
                    'Anual' => 'Anual',
                    '1C' => '1C',
                    '2C' => '2C',
                ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('planEstudio.nombre')->label('Plan de Estudio'),
                Tables\Columns\TextColumn::make('anio')->label('Año'),
                Tables\Columns\TextColumn::make('modalidad')->label('Modalidad'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterias::route('/'),
            'create' => Pages\CreateMateria::route('/create'),
            'edit' => Pages\EditMateria::route('/{record}/edit'),
        ];
    }
}
