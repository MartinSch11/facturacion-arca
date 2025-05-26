<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioPsicopedagogiaSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 8,
            'nombre' => 'Plan 2024 - Lic. en Psicopedagogía',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Introducción a la Psicopedagogía', '1C'],
                ['Psicología General', '1C'],
                ['Neuroanatomía', '2C'],
                ['Pedagogía', '2C'],
                ['Observación y Práctica Inicial', 'Anual'],
            ],
            2 => [
                ['Psicología Evolutiva I', '1C'],
                ['Psicología Educacional', '1C'],
                ['Evaluación Psicopedagógica I', '2C'],
                ['Neurofisiología del Aprendizaje', '2C'],
                ['Psicología Social', 'Anual'],
            ],
            3 => [
                ['Psicología Evolutiva II', '1C'],
                ['Evaluación Psicopedagógica II', '1C'],
                ['Dificultades del Aprendizaje', '2C'],
                ['Didáctica Especial', '2C'],
                ['Práctica Profesional I', 'Anual'],
            ],
            4 => [
                ['Diagnóstico Institucional', '1C'],
                ['Práctica Profesional II', '2C'],
                ['Psicopatología Infantil', '2C'],
                ['Intervención Psicopedagógica', 'Anual'],
            ],
            5 => [
                ['Práctica Profesional Supervisada', 'Anual'],
                ['Seminario de Integración', '1C'],
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
            ['Psicología Evolutiva I', 'Psicología General'],
            ['Evaluación Psicopedagógica I', 'Introducción a la Psicopedagogía'],
            ['Evaluación Psicopedagógica II', 'Evaluación Psicopedagógica I'],
            ['Dificultades del Aprendizaje', 'Neurofisiología del Aprendizaje'],
            ['Práctica Profesional I', 'Evaluación Psicopedagógica II'],
            ['Práctica Profesional II', 'Práctica Profesional I'],
            ['Intervención Psicopedagógica', 'Dificultades del Aprendizaje'],
            ['Práctica Profesional Supervisada', 'Intervención Psicopedagógica'],
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
