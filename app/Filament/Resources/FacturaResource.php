<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Filament\Resources\FacturaResource\RelationManagers;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\TextInput::make('id_alumno')
                    ->numeric()
                    ->default(null),
                Forms\Components\DatePicker::make('fecha_emision'),
                Forms\Components\DatePicker::make('periodo_desde'),
                Forms\Components\DatePicker::make('periodo_hasta'),
                Forms\Components\DatePicker::make('fecha_vencimiento'),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('estado'),
                Forms\Components\TextInput::make('cae')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\DatePicker::make('cae_vencimiento'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_alumno')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_emision')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodo_desde')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodo_hasta')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('cae')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cae_vencimiento')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacturas::route('/'),
            'create' => Pages\CreateFactura::route('/create'),
            'edit' => Pages\EditFactura::route('/{record}/edit'),
        ];
    }
}
