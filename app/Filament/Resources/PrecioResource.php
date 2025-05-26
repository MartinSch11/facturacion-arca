<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrecioResource\Pages;
use App\Models\Carrera;
use App\Models\Concepto;
use App\Models\Precio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class PrecioResource extends Resource
{
    protected static ?string $model = Precio::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

        public static function getSlug(): string
    {
        return 'precios';
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
                Forms\Components\Select::make('id_carrera')
                    ->relationship('carrera', 'nombre')
                    ->required()
                    ->label('Carrera'),
                Forms\Components\TextInput::make('precio')
                    ->required()
                    ->numeric()
                    ->label('Precio'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('carrera.nombre')->label('Carrera'),
                Tables\Columns\TextColumn::make('precio')->label('Precio'),
            ])
            ->filters([
                // Puedes agregar filtros aquí
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->headerActions([
                Action::make('aumentar-precios')
                    ->form([
                        Forms\Components\TextInput::make('porcentaje_aumento')
                            ->required()
                            ->maxLength(5)
                            ->label('Cantidad de Aumento (%)'),
                        Forms\Components\Select::make('id_carreras')
                            ->options(Carrera::pluck('nombre', 'id_carrera')->toArray())
                            ->multiple()
                            ->label('Carreras')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $aumento = $data['porcentaje_aumento'];
                        $carreras = $data['id_carreras'];

                        $precios = Precio::whereIn('id_carrera', $carreras)->get();
                        foreach ($precios as $precio) {
                            $nuevoPrecio = $precio->precio * (1 + $aumento / 100);
                            $precio->update(['precio' => $nuevoPrecio]);
                        }

                        Notification::make()
                            ->title('Precios actualizados')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-arrow-up')
                    ->label('Aumentar Precios')
                    ->modalHeading('Aumentar Precios')
                    ->modalButton('Aumentar')
                    ->modalWidth('lg'),

                Action::make('disminuir-precios')
                    ->form([
                        Forms\Components\TextInput::make('porcentaje_disminucion')
                            ->required()
                            ->maxLength(3)
                            ->label('Cantidad de Disminución (%)'),
                        Forms\Components\Select::make('id_carreras')
                            ->options(Carrera::pluck('nombre', 'id_carrera')->toArray())
                            ->multiple()
                            ->label('Carreras')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $disminucion = $data['porcentaje_disminucion'];
                        $carreras = $data['id_carreras'];

                        $precios = Precio::whereIn('id_carrera', $carreras)->get();
                        foreach ($precios as $precio) {
                            $nuevoPrecio = $precio->precio * (1 - $disminucion / 100);
                            $precio->update(['precio' => $nuevoPrecio]);
                        }

                        Notification::make()
                            ->title('Precios actualizados')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-arrow-down')
                    ->label('Disminuir Precios')
                    ->modalHeading('Disminuir Precios')
                    ->modalButton('Disminuir')
                    ->modalWidth('lg'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrecios::route('/'),
            'create' => Pages\CreatePrecio::route('/create'),
            'edit' => Pages\EditPrecio::route('/{record}/edit'),
        ];
    }
}
