<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioKinesiologiaSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 9,
            'nombre' => 'Plan 2024 - Lic. en Kinesiología y Fisiatría',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Anatomía Humana', '1C'],
                ['Biología Celular', '1C'],
                ['Fisiología Humana', '2C'],
                ['Psicología General', '2C'],
                ['Introducción a la Kinesiología', 'Anual'],
            ],
            2 => [
                ['Biomecánica', '1C'],
                ['Fisiología del Ejercicio', '1C'],
                ['Técnicas de Evaluación Funcional', '2C'],
                ['Cinesiología', '2C'],
                ['Práctica I - Sala de Rehabilitación', 'Anual'],
            ],
            3 => [
                ['Kinesioterapia', '1C'],
                ['Electroterapia', '1C'],
                ['Ortopedia y Traumatología', '2C'],
                ['Neuroanatomía', '2C'],
                ['Práctica II - Internación y Ambulatorio', 'Anual'],
            ],
            4 => [
                ['Kinesiología Neurológica', '1C'],
                ['Terapia Manual', '1C'],
                ['Práctica III - Especialidades', '2C'],
                ['Clínica Kinésica', 'Anual'],
            ],
            5 => [
                ['Seminario de Investigación', '1C'],
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
            ['Fisiología Humana', 'Biología Celular'],
            ['Biomecánica', 'Anatomía Humana'],
            ['Fisiología del Ejercicio', 'Fisiología Humana'],
            ['Cinesiología', 'Biomecánica'],
            ['Kinesioterapia', 'Cinesiología'],
            ['Electroterapia', 'Fisiología del Ejercicio'],
            ['Práctica II - Internación y Ambulatorio', 'Práctica I - Sala de Rehabilitación'],
            ['Kinesiología Neurológica', 'Neuroanatomía'],
            ['Práctica III - Especialidades', 'Práctica II - Internación y Ambulatorio'],
            ['Clínica Kinésica', 'Ortopedia y Traumatología'],
            ['Taller de Tesis', 'Seminario de Investigación'],
            ['Práctica Profesional Supervisada', 'Práctica III - Especialidades'],
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
