<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioLicenciaturaMarketingSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el plan de estudio
    $plan = PlanEstudio::create([
        'id_carrera' => 3,
        'nombre' => 'Plan 2024 - Lic. en Marketing',
        'anio_implementacion' => 2024,
    ]);

        // Lista de materias por año
         $materiasPorAnio = [
        1 => [
            ['Fundamentos del Marketing', '1C'],
            ['Sociología', '1C'],
            ['Contabilidad', '2C'],
            ['Matemática I', '2C'],
            ['Psicología del Consumidor', 'Anual'],
        ],
        2 => [
            ['Estadística Aplicada', '1C'],
            ['Investigación de Mercado I', '1C'],
            ['Comportamiento del Consumidor', '2C'],
            ['Estrategias de Precio', '2C'],
            ['Marketing Digital', 'Anual'],
        ],
        3 => [
            ['Planificación Estratégica', '1C'],
            ['Investigación de Mercado II', '1C'],
            ['Publicidad y Promoción', '2C'],
            ['Canales de Distribución', '2C'],
            ['Marketing de Servicios', 'Anual'],
        ],
        4 => [
            ['Branding', '1C'],
            ['Marketing Internacional', '2C'],
            ['Análisis de Datos', '2C'],
            ['Gestión de Producto', 'Anual'],
        ],
        5 => [
            ['Seminario de Tendencias', '1C'],
            ['Práctica Profesional Supervisada', 'Anual'],
            ['Taller de Tesis', '2C'],
        ],
    ];

    $materiasCreadas = [];

    foreach ($materiasPorAnio as $anio => $materias) {
        foreach ($materias as [$nombre, $modalidad]) {
            $materiasCreadas[$nombre] = Materia::create([
                'id_plan_estudio' => $plan->id,
                'nombre' => $nombre,
                'anio' => $anio,
                'modalidad' => $modalidad,
                'cupo' => 40,
            ]);
        }
    }

    $correlativas = [
        ['Investigación de Mercado II', 'Investigación de Mercado I'],
        ['Comportamiento del Consumidor', 'Psicología del Consumidor'],
        ['Estrategias de Precio', 'Contabilidad'],
        ['Marketing Internacional', 'Planificación Estratégica'],
        ['Taller de Tesis', 'Marketing de Servicios'],
        ['Práctica Profesional Supervisada', 'Branding'],
    ];

    foreach ($correlativas as [$materia, $requiere]) {
        DB::table('materias_correlativas')->insert([
            'id_materia' => $materiasCreadas[$materia]->id,
            'id_correlativa' => $materiasCreadas[$requiere]->id,
        ]);
    }

    foreach ($materiasCreadas as $materia) {
        ComisionMateria::create([
            'id_materia' => $materia->id,
            'division' => 'A',
            'turno' => 'M',
            'periodo' => '2024-' . ($materia->modalidad === '2C' ? '2C' : '1C'),
            'cupo' => $materia->cupo,
        ]);
    }
}
}
