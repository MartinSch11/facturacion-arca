<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Carrera;
use App\Models\Cursada;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PieChartWidget extends ApexChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $chartId = 'aprobadosPorCarreraChart';
    protected static ?string $heading = 'Estados de Cursada por Carrera';

    public ?int $carrera = null;
    public ?int $anio = null;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('carrera')
                ->label('Carrera')
                ->options(Carrera::pluck('nombre', 'id_carrera'))
                ->searchable()
                ->reactive()
                ->placeholder('Todas las carreras'),

            Forms\Components\TextInput::make('anio')
                ->label('Año de la materia')
                ->numeric()
                ->placeholder('Ej: 1, 2...')
                ->reactive()
                ->rules(['integer', 'between:1,5'])
                ->validationMessages([
                    'integer' => 'Debe ser número entero.',
                    'between' => 'Debe estar entre 1 y 5.',
                ]),
        ];
    }

    public function filtersUpdated(): void
    {
        $this->updateChart();
    }

    protected function getOptions(): array
    {
        $filters = $this->form->getState();

        $carrera = $filters['carrera'] ?? null;
        $anio = $filters['anio'] ?? null;

        $query = Cursada::query()
            ->join('materias', 'cursadas.id_materia', '=', 'materias.id')
            ->join('planes_estudio', 'materias.id_plan_estudio', '=', 'planes_estudio.id')
            ->join('carrera', 'planes_estudio.id_carrera', '=', 'carrera.id_carrera');

        if ($carrera) {
            $query->where('carrera.id_carrera', $carrera);
        }

        if ($anio) {
            $query->where('materias.anio', $anio);
        }

        $estados = $query->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $labels = ['Cursando', 'Aprobada', 'Desaprobada', 'Abandonada'];
        $series = array_map(fn($estado) => $estados[$estado] ?? 0, $labels);

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $series,
            'labels' => $labels,
        ];
    }
}
