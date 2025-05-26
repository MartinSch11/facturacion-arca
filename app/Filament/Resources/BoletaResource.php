<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoletaResource\Pages;
use App\Models\Boleta;
use App\Models\Carrera;
use App\Services\BoletaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Contracts\Container\BindingResolutionException;
use Filament\Navigation\NavigationItem;

class BoletaResource extends Resource
{
    protected static ?string $model = Boleta::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

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
        return $form->schema([
            Forms\Components\Section::make('Crear Boletas')
                ->schema([
                    Forms\Components\DatePicker::make('fecha_primer_vencimiento')->required(),
                    Forms\Components\DatePicker::make('fecha_segundo_vencimiento'),
                    Forms\Components\DatePicker::make('fecha_tercer_vencimiento'),
                    Forms\Components\Select::make('id_carreras')
                        ->options(Carrera::pluck('nombre', 'id_carrera')->toArray())
                        ->multiple()
                        ->required(),
                    Forms\Components\Select::make('id_concepto')
                        ->relationship('concepto', 'nombre')
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('carreraXAlumno.carrera.nombre')->label('Carrera')->sortable(),
                Tables\Columns\TextColumn::make('concepto.nombre')->label('Concepto')->sortable(),
                Tables\Columns\TextColumn::make('importe_pagado')->label('Precio')->money('ars', true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_concepto')
                    ->relationship('concepto', 'nombre')
                    ->label('Filtrar por Concepto'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('crear_boletas')
                    ->label('Crear Boletas')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_primer_vencimiento')->required(),
                        Forms\Components\DatePicker::make('fecha_segundo_vencimiento'),
                        Forms\Components\DatePicker::make('fecha_tercer_vencimiento'),
                        Forms\Components\Select::make('id_carreras')
                            ->options(Carrera::pluck('nombre', 'id_carrera')->toArray())
                            ->multiple()
                            ->required(),
                        Forms\Components\Select::make('id_concepto')
                            ->relationship('concepto', 'nombre')
                            ->required(),
                    ])
                    ->action(function (array $data) {

                        $service = app(BoletaService::class);

                        try {
                            $boletasCreadas = $service->generarBoletas($data);

                            Notification::make()
                                ->title('Boletas creadas')
                                ->body($boletasCreadas > 0
                                    ? "Se crearon {$boletasCreadas} boletas exitosamente."
                                    : "No se crearon boletas. Verifica los datos.")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('No se pudieron crear las boletas: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Crear')
                    ->modalHeading('Crear Boletas Masivas'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBoletas::route('/'),
        ];
    }
}
