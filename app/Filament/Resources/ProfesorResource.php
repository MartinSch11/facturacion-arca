<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfesorResource\Pages;
use App\Models\Profesor;
use Filament\Forms;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;

class ProfesorResource extends Resource
{
    protected static ?string $model = Profesor::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Profesores';

    public static function getSlug(): string
    {
        return 'profesores';
    }
    public static function getModelLabel(): string
    {
        return 'Profesor';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Profesores';
    }

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
            Forms\Components\TextInput::make('dni')->label('DNI')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('nombre')->required()->maxLength(100),
            Forms\Components\TextInput::make('apellido')->required()->maxLength(100),
            Forms\Components\TextInput::make('email')->email()->maxLength(100),
            Forms\Components\TextInput::make('telefono')->maxLength(20),
            Forms\Components\TextInput::make('titulo')->label('Título Académico')->maxLength(150),
        ])->columns(2);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('dni')->label('DNI')->searchable(),
            Tables\Columns\TextColumn::make('apellido')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('nombre')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('email'),
            Tables\Columns\TextColumn::make('telefono'),
            Tables\Columns\TextColumn::make('titulo')->label('Título'),
        ])->actions([
                    Tables\Actions\EditAction::make(),
                ])->bulkActions([
                ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfesors::route('/'),
            'create' => Pages\CreateProfesor::route('/create'),
            'edit' => Pages\EditProfesor::route('/{record}/edit'),
        ];
    }
}
