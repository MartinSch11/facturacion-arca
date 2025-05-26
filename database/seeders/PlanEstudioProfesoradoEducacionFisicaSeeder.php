<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioProfesoradoEducacionFisicaSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 7,
            'nombre' => 'Plan 2024 - Profesorado en Educación Física y Deportes',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Anatomía Humana', '1C'],
                ['Psicología del Desarrollo', '1C'],
                ['Didáctica General', '2C'],
                ['Fundamentos de la Educación Física', '2C'],
                ['Taller Corporal y Motricidad', 'Anual'],
            ],
            2 => [
                ['Fisiología del Ejercicio', '1C'],
                ['Biomecánica', '1C'],
                ['Pedagogía', '2C'],
                ['Práctica Docente I', '2C'],
                ['Teoría del Entrenamiento Deportivo', 'Anual'],
            ],
            3 => [
                ['Evaluación Educativa', '1C'],
                ['Didáctica de la Educación Física I', '1C'],
                ['Práctica Docente II', '2C'],
                ['Psicomotricidad y Juego', '2C'],
                ['Sociología de la Educación', 'Anual'],
            ],
            4 => [
                ['Didáctica de la Educación Física II', '1C'],
                ['Práctica Docente Final', '2C'],
                ['Ética Profesional Docente', '2C'],
                ['Planificación Curricular', 'Anual'],
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
                    'cupo' => 30,
                ]);
            }
        }

        $correlativas = [
            ['Fisiología del Ejercicio', 'Anatomía Humana'],
            ['Biomecánica', 'Anatomía Humana'],
            ['Práctica Docente I', 'Didáctica General'],
            ['Didáctica de la Educación Física I', 'Pedagogía'],
            ['Práctica Docente II', 'Práctica Docente I'],
            ['Didáctica de la Educación Física II', 'Didáctica de la Educación Física I'],
            ['Práctica Docente Final', 'Práctica Docente II'],
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
