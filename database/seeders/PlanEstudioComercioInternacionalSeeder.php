<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioComercioInternacionalSeeder extends Seeder
{

    public function run(): void
    {

        $plan = PlanEstudio::create([
            'id_carrera' => 2,
            'nombre' => 'Plan 2024 - Lic. en Comercio Internacional',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Introducción al Comercio Internacional', '1C'],
                ['Economía General', '1C'],
                ['Contabilidad I', '2C'],
                ['Matemática', '2C'],
                ['Geografía Económica', 'Anual'],
            ],
            2 => [
                ['Marketing Internacional', '1C'],
                ['Comercio Exterior y Aduanas', '1C'],
                ['Estadística', '2C'],
                ['Contabilidad II', '2C'],
                ['Derecho Comercial', 'Anual'],
            ],
            3 => [
                ['Logística Internacional', '1C'],
                ['Microeconomía', '1C'],
                ['Finanzas Internacionales', '2C'],
                ['Sistemas de Información', '2C'],
                ['Derecho Internacional Público', 'Anual'],
            ],
            4 => [
                ['Negociación Internacional', '1C'],
                ['Macroeconomía', '2C'],
                ['Análisis de Costos', '2C'],
                ['Seminario de Actualidad Económica', 'Anual'],
            ],
            5 => [
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
                ]);
            }
        }

        // Correlativas básicas
        $correlativas = [
            ['Contabilidad II', 'Contabilidad I'],
            ['Marketing Internacional', 'Introducción al Comercio Internacional'],
            ['Finanzas Internacionales', 'Estadística'],
            ['Análisis de Costos', 'Contabilidad II'],
            ['Práctica Profesional Supervisada', 'Negociación Internacional'],
            ['Taller de Tesis', 'Seminario de Actualidad Económica'],
        ];

        foreach ($correlativas as [$materia, $requiere]) {
            DB::table('materias_correlativas')->insert([
                'id_materia' => $materiasCreadas[$materia]->id,
                'id_correlativa' => $materiasCreadas[$requiere]->id,
            ]);
        }

        // Crear comisiones
        foreach ($materiasCreadas as $materia) {
            ComisionMateria::create([
                'id_materia' => $materia->id,
                'division' => 'A',
                'turno' => 'T',
                'periodo' => '2024-' . ($materia->modalidad === '2C' ? '2C' : '1C'),
                'cupo' => 40, // Valor por defecto para cupo
            ]);
        }
    }
}
