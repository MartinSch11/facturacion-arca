<?php

namespace App\Filament\Alumno\Resources;

use App\Filament\Alumno\Resources\DetalleFacturaResource\Pages;
use App\Filament\Alumno\Resources\DetalleFacturaResource\RelationManagers;
use App\Models\DetalleFactura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetalleFacturaResource extends Resource
{
    protected static ?string $model = DetalleFactura::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            ->columns([
                //
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

    public static function shouldRegisterNavigation(): bool{
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
