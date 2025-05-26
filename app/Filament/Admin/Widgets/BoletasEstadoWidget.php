<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Boleta;
use App\Models\Factura;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BoletasEstadoWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Boletas Pagadas', Boleta::where('estado', 'pagado')->count())
                ->color('success'),
            Stat::make('Boletas Pendientes', Boleta::where('estado', 'pendiente')->count())
                ->color('danger'),
            Stat::make('Facturas Pendientes', Factura::where('estado', 'pendiente')->count())
        ];
    }
}
