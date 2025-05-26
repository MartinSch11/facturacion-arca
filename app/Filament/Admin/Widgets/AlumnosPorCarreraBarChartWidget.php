<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Alumno;
use App\Models\Carrera;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AlumnosPorCarreraBarChartWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'alumnosPorCarreraBarChart';
    protected static ?string $heading = 'Cantidad de Alumnos por Carrera';
    protected static ?int $sort = 2;

    protected function getOptions(): array
    {
        $data = Carrera::query()
            ->leftJoin('CarreraXAlumno', 'Carrera.id_carrera', '=', 'CarreraXAlumno.id_carrera')
            ->select('Carrera.nombre', DB::raw('COUNT(CarreraXAlumno.dni_alumno) as total'))
            ->groupBy('Carrera.id_carrera', 'Carrera.nombre')
            ->orderBy('Carrera.nombre')
            ->get();

        // Abreviar nombres: usar las 3 primeras letras de cada palabra
        $labels = $data->pluck('nombre')->map(function ($nombre) {
            return implode(' ', array_map(fn($w) => mb_substr($w, 0, 3), explode(' ', $nombre)));
        })->toArray();

        $series = $data->pluck('total')->map(fn($v) => (int)$v)->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Alumnos',
                    'data' => $series,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
            ],
        ];
    }
}
