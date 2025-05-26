<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfiguracionResource\Pages;
use App\Filament\Resources\ConfiguracionResource\RelationManagers;
use App\Models\Configuracion;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConfiguracionResource extends Resource
{
    protected static ?string $model = Configuracion::class;
    protected static ?string $navigationLabel = 'Configuración';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function getSlug(): string
    {
        return 'configuración';
    }

    public static function getModelLabel(): string
    {
        return 'Configuración';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Configuración';
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
                Forms\Components\TextInput::make('cuit')
                    ->required()
                    ->maxLength(20)
                    ->label('CUIT'),
                Forms\Components\TextInput::make('punto_venta')
                    ->required()
                    ->numeric()
                    ->label('Punto de Venta'),
                Forms\Components\DatePicker::make('fecha_inicio_inscripcion_1c')
                    ->label('Fecha inicio inscripción 1C')
                    ->required()
                    ->helperText('Fecha a partir de la cual se habilitará la inscripción a comisiones del primer cuatrimestre.'),
                Forms\Components\DatePicker::make('fecha_inicio_inscripcion_2c')
                    ->label('Fecha inicio inscripción 2C')
                    ->required()
                    ->helperText('Fecha a partir de la cual se habilitará la inscripción a comisiones del segundo cuatrimestre.'),
                Toggle::make('habilitar_inscripcion_comisiones')
                    ->label('Habilitar inscripción a comisiones')
                    ->helperText('Si está desactivado, los alumnos no verán el recurso de inscripción.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cuit')->label('CUIT'),
                Tables\Columns\TextColumn::make('punto_venta')->label('Punto de Venta'),
                Tables\Columns\TextColumn::make('fecha_inicio_inscripcion_1c')->label('Inicio Inscripción 1C'),
                Tables\Columns\TextColumn::make('fecha_inicio_inscripcion_2c')->label('Inicio Inscripción 2C'),
                Tables\Columns\IconColumn::make('habilitar_inscripcion_comisiones')
                    ->label('Inscripción Habilitada')
                    ->boolean(),
            ])
            ->filters([
                // Puedes agregar filtros aquí
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfiguracions::route('/'),
            'create' => Pages\CreateConfiguracion::route('/create'),
            'edit' => Pages\EditConfiguracion::route('/{record}/edit'),
        ];
    }
}
