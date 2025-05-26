<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetalleFacturaResource\Pages;
use App\Filament\Resources\DetalleFacturaResource\RelationManagers;
use App\Models\DetalleFactura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetalleFacturaResource extends Resource
{
    protected static ?string $model = DetalleFactura::class;
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
                Forms\Components\TextInput::make('id_factura')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('concepto')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\TextInput::make('cantidad')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('precio_unitario')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_factura')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('concepto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_unitario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
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
            'index' => Pages\ListDetalleFacturas::route('/'),
            'create' => Pages\CreateDetalleFactura::route('/create'),
            'edit' => Pages\EditDetalleFactura::route('/{record}/edit'),
        ];
    }
}
