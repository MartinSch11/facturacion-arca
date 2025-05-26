<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioTerapiaOcupacionalSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 11,
            'nombre' => 'Plan 2024 - Lic. en Terapia Ocupacional',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Introducción a la Terapia Ocupacional', '1C'],
                ['Psicología General', '1C'],
                ['Biología Humana', '2C'],
                ['Sociología', '2C'],
                ['Fundamentos de Salud y Discapacidad', 'Anual'],
            ],
            2 => [
                ['Neurofisiología', '1C'],
                ['Psicopatología', '1C'],
                ['Técnicas de Evaluación Funcional', '2C'],
                ['Psicología Evolutiva', '2C'],
                ['Práctica I - Comunidad', 'Anual'],
            ],
            3 => [
                ['Ergonomía y Diseño Funcional', '1C'],
                ['Terapia Ocupacional Infantil', '1C'],
                ['Intervención en Discapacidad Motriz', '2C'],
                ['Terapia Ocupacional Adultos', '2C'],
                ['Práctica II - Instituciones', 'Anual'],
            ],
            4 => [
                ['Terapia Ocupacional Gerontológica', '1C'],
                ['Ética Profesional y Legislación', '2C'],
                ['Seminario de Intervención Comunitaria', '2C'],
                ['Práctica III - Hospitalaria', 'Anual'],
            ],
            5 => [
                ['Seminario de Integración Profesional', '1C'],
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
                    'cupo' => 30,
                ]);
            }
        }

        $correlativas = [
            ['Neurofisiología', 'Biología Humana'],
            ['Psicopatología', 'Psicología General'],
            ['Técnicas de Evaluación Funcional', 'Introducción a la Terapia Ocupacional'],
            ['Terapia Ocupacional Infantil', 'Psicología Evolutiva'],
            ['Intervención en Discapacidad Motriz', 'Neurofisiología'],
            ['Práctica II - Instituciones', 'Práctica I - Comunidad'],
            ['Terapia Ocupacional Gerontológica', 'Terapia Ocupacional Adultos'],
            ['Práctica III - Hospitalaria', 'Práctica II - Instituciones'],
            ['Taller de Tesis', 'Seminario de Integración Profesional'],
            ['Práctica Profesional Supervisada', 'Práctica III - Hospitalaria'],
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
