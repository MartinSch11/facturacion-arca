<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioNutricionSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 10,
            'nombre' => 'Plan 2024 - Lic. en Nutrición',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Biología Humana', '1C'],
                ['Química General', '1C'],
                ['Anatomía Humana', '2C'],
                ['Bioquímica', '2C'],
                ['Introducción a la Nutrición', 'Anual'],
            ],
            2 => [
                ['Fisiología Humana', '1C'],
                ['Bromatología', '1C'],
                ['Psicología General', '2C'],
                ['Microbiología y Parasitología', '2C'],
                ['Nutrición Normal', 'Anual'],
            ],
            3 => [
                ['Dietoterapia I', '1C'],
                ['Salud Pública I', '1C'],
                ['Educación Alimentaria', '2C'],
                ['Técnica Dietética', '2C'],
                ['Práctica I - Centros de Salud', 'Anual'],
            ],
            4 => [
                ['Dietoterapia II', '1C'],
                ['Salud Pública II', '2C'],
                ['Legislación Alimentaria', '2C'],
                ['Práctica II - Hospitalaria', 'Anual'],
            ],
            5 => [
                ['Seminario de Integración Nutricional', '1C'],
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
                    'cupo' => 35,
                ]);
            }
        }

        $correlativas = [
            ['Anatomía Humana', 'Biología Humana'],
            ['Bioquímica', 'Química General'],
            ['Fisiología Humana', 'Anatomía Humana'],
            ['Nutrición Normal', 'Fisiología Humana'],
            ['Dietoterapia I', 'Nutrición Normal'],
            ['Dietoterapia II', 'Dietoterapia I'],
            ['Salud Pública II', 'Salud Pública I'],
            ['Práctica II - Hospitalaria', 'Práctica I - Centros de Salud'],
            ['Taller de Tesis', 'Seminario de Integración Nutricional'],
            ['Práctica Profesional Supervisada', 'Dietoterapia II'],
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
