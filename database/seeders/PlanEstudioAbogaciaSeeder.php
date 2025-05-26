<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioAbogaciaSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 12,
            'nombre' => 'Plan 2024 - Abogacía',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Introducción al Derecho', '1C'],
                ['Derecho Romano', '1C'],
                ['Teoría del Estado', '2C'],
                ['Historia del Derecho', '2C'],
                ['Sociología Jurídica', 'Anual'],
            ],
            2 => [
                ['Derecho Constitucional', '1C'],
                ['Derecho Civil I - Personas', '1C'],
                ['Derecho Penal I - Parte General', '2C'],
                ['Derecho Procesal I', '2C'],
                ['Filosofía del Derecho', 'Anual'],
            ],
            3 => [
                ['Derecho Civil II - Obligaciones', '1C'],
                ['Derecho Penal II - Parte Especial', '1C'],
                ['Derecho Procesal II', '2C'],
                ['Derecho Comercial I', '2C'],
                ['Práctica Forense I', 'Anual'],
            ],
            4 => [
                ['Derecho Civil III - Contratos', '1C'],
                ['Derecho Comercial II - Sociedades', '1C'],
                ['Derecho Administrativo', '2C'],
                ['Derecho Laboral', '2C'],
                ['Práctica Forense II', 'Anual'],
            ],
            5 => [
                ['Derecho Internacional Público', '1C'],
                ['Derecho Tributario', '2C'],
                ['Derechos Humanos y Garantías', '2C'],
                ['Taller de Tesis', 'Anual'],
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
                    'cupo' => 45,
                ]);
            }
        }

        $correlativas = [
            ['Derecho Constitucional', 'Teoría del Estado'],
            ['Derecho Civil I - Personas', 'Introducción al Derecho'],
            ['Derecho Penal I - Parte General', 'Introducción al Derecho'],
            ['Derecho Civil II - Obligaciones', 'Derecho Civil I - Personas'],
            ['Derecho Penal II - Parte Especial', 'Derecho Penal I - Parte General'],
            ['Derecho Procesal II', 'Derecho Procesal I'],
            ['Derecho Comercial I', 'Derecho Civil II - Obligaciones'],
            ['Práctica Forense II', 'Práctica Forense I'],
            ['Derecho Tributario', 'Derecho Comercial II - Sociedades'],
            ['Taller de Tesis', 'Derecho Administrativo'],
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
