<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioContadorPublicoSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 5,
            'nombre' => 'Plan 2024 - Contador Público Nacional',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Contabilidad I', '1C'],
                ['Matemática I', '1C'],
                ['Derecho Privado', '2C'],
                ['Economía General', '2C'],
                ['Administración General', 'Anual'],
            ],
            2 => [
                ['Contabilidad II', '1C'],
                ['Estadística I', '1C'],
                ['Derecho Laboral', '2C'],
                ['Matemática II', '2C'],
                ['Microeconomía', 'Anual'],
            ],
            3 => [
                ['Contabilidad III', '1C'],
                ['Finanzas Públicas', '1C'],
                ['Derecho Tributario', '2C'],
                ['Estadística II', '2C'],
                ['Macroeconomía', 'Anual'],
            ],
            4 => [
                ['Contabilidad de Costos', '1C'],
                ['Auditoría I', '2C'],
                ['Impuestos I', '2C'],
                ['Teoría Contable', 'Anual'],
            ],
            5 => [
                ['Auditoría II', '1C'],
                ['Impuestos II', '2C'],
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
            ['Contabilidad II', 'Contabilidad I'],
            ['Contabilidad III', 'Contabilidad II'],
            ['Auditoría I', 'Contabilidad III'],
            ['Auditoría II', 'Auditoría I'],
            ['Impuestos II', 'Impuestos I'],
            ['Taller de Tesis', 'Teoría Contable'],
            ['Práctica Profesional Supervisada', 'Contabilidad de Costos'],
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
