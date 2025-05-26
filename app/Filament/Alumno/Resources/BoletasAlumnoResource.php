<?php
namespace App\Filament\Alumno\Resources;

use App\Filament\Alumno\Pages\PagoBoleta;
use App\Filament\Alumno\Resources\BoletasAlumnoResource\Pages;
use App\Models\BoletasAlumno;
use App\Models\CarreraXAlumno;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BoletasAlumnoResource extends Resource
{
    protected static ?string $model = BoletasAlumno::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Mis Boletas';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('carreraXAlumno.carrera.nombre')
                    ->label('Carrera')
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('boleta.nombre_detallado')
                    ->label('Concepto')
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('importe_pagado')
                    ->label('Precio')
                    ->money('ars', true),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                        default => ucfirst($state ?? 'Desconocido'),
                    }),
                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Fecha de Pago')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'pagado' => 'Pagado',
                    ])
                    ->label('Filtrar por Estado'),
                Tables\Filters\SelectFilter::make('carrera')
                    ->label('Filtrar por Carrera')
                    ->options(
                        fn() => Auth::user()?->role === 'alumno'
                        ? CarreraXAlumno::where('dni_alumno', Auth::user()->dni)
                            ->with('carrera')
                            ->get()
                            ->pluck('carrera.nombre', 'carrera.id_carrera')
                            ->toArray()
                        : []
                    )
                    ->query(
                        fn(Builder $query, array $data) => isset($data['value'])
                        ? $query->whereHas('carreraXAlumno', fn($q) => $q->where('id_carrera', $data['value']))
                        : $query
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('pagar')
                    ->label('Pagar')
                    ->icon('heroicon-m-currency-dollar')
                    ->color('warning')
                    ->url(fn($record) => PagoBoleta::getUrl(['boleta' => $record->nro_boleta]))
                    ->visible(fn($record) => $record->estado === 'pendiente'),

                    Tables\Actions\Action::make('facturando')
                    ->label('Facturando...')
                    ->icon('heroicon-m-arrow-path')
                    ->color('blue')
                    ->disabled()
                    ->extraAttributes([
                        'class' => 'pointer-events-none',
                    ])
                    ->visible(function ($record) {
                        return $record->estado === 'pagado'
                            && $record->detalleFactura
                            && optional($record->detalleFactura->factura)->estado === 'pendiente';
                    }),
                
                Tables\Actions\Action::make('descargar_factura')
                    ->label('Descargar factura')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn($record) => route('descargar-factura', $record->detalleFactura->nro_factura))
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        return $record->estado === 'pagado'
                            && $record->detalleFactura
                            && optional($record->detalleFactura->factura)->estado === 'procesada';
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoletasAlumnos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (Auth::user()?->role === 'alumno') {
            $matriculas = CarreraXAlumno::where('dni_alumno', Auth::user()->dni)
                ->pluck('matricula')
                ->toArray();
            $query->whereIn('matricula', $matriculas);
        }
        return $query;
    }
}
