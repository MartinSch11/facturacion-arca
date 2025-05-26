<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioCorretajeInmobiliarioSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 6,
            'nombre' => 'Plan 2024 - Lic. en Corretaje y Negocios Inmobiliarios',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Introducción al Derecho', '1C'],
                ['Administración General', '1C'],
                ['Principios de Economía', '2C'],
                ['Matemática Financiera', '2C'],
                ['Técnicas de Negociación', 'Anual'],
            ],
            2 => [
                ['Contabilidad General', '1C'],
                ['Legislación Inmobiliaria', '1C'],
                ['Derecho Civil', '2C'],
                ['Tasaciones', '2C'],
                ['Marketing Inmobiliario', 'Anual'],
            ],
            3 => [
                ['Contratos Inmobiliarios', '1C'],
                ['Derecho Registral', '1C'],
                ['Gestión Inmobiliaria', '2C'],
                ['Urbanismo y Planificación', '2C'],
                ['Finanzas Aplicadas', 'Anual'],
            ],
            4 => [
                ['Gestión de Carteras', '1C'],
                ['Aspectos Impositivos del Sector', '2C'],
                ['Legislación Laboral', '2C'],
                ['Evaluación de Proyectos Inmobiliarios', 'Anual'],
            ],
            5 => [
                ['Taller de Ética Profesional', '1C'],
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
            ['Derecho Civil', 'Introducción al Derecho'],
            ['Legislación Inmobiliaria', 'Introducción al Derecho'],
            ['Contratos Inmobiliarios', 'Derecho Civil'],
            ['Derecho Registral', 'Legislación Inmobiliaria'],
            ['Aspectos Impositivos del Sector', 'Contabilidad General'],
            ['Taller de Tesis', 'Evaluación de Proyectos Inmobiliarios'],
            ['Práctica Profesional Supervisada', 'Gestión Inmobiliaria'],
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
