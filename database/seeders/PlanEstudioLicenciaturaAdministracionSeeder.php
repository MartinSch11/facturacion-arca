<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioLicenciaturaAdministracionSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 4,
            'nombre' => 'Plan 2024 - Lic. en Administración',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Administración I', '1C'],
                ['Contabilidad I', '1C'],
                ['Economía General', '2C'],
                ['Matemática I', '2C'],
                ['Derecho Privado', 'Anual'],
            ],
            2 => [
                ['Administración II', '1C'],
                ['Contabilidad II', '1C'],
                ['Matemática Financiera', '2C'],
                ['Estadística', '2C'],
                ['Derecho Laboral', 'Anual'],
            ],
            3 => [
                ['Finanzas I', '1C'],
                ['Marketing', '1C'],
                ['Costos', '2C'],
                ['Informática Aplicada', '2C'],
                ['Comportamiento Organizacional', 'Anual'],
            ],
            4 => [
                ['Finanzas II', '1C'],
                ['Administración Estratégica', '2C'],
                ['Investigación Operativa', '2C'],
                ['Gestión de RR.HH.', 'Anual'],
            ],
            5 => [
                ['Seminario de Administración Pública', '1C'],
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
            ['Administración II', 'Administración I'],
            ['Finanzas I', 'Contabilidad II'],
            ['Costos', 'Contabilidad II'],
            ['Finanzas II', 'Finanzas I'],
            ['Administración Estratégica', 'Administración II'],
            ['Taller de Tesis', 'Administración Estratégica'],
            ['Práctica Profesional Supervisada', 'Gestión de RR.HH.'],
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
