<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioArquitecturaSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 14, // Arquitectura
            'nombre' => 'Plan 2024 - Arquitectura',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Introducción a la Arquitectura', '1C'],
                ['Matemática I', '1C'],
                ['Dibujo Técnico', '2C'],
                ['Historia de la Arquitectura I', '2C'],
                ['Materiales de Construcción', 'Anual'],
            ],
            2 => [
                ['Diseño Arquitectónico I', '1C'],
                ['Matemática II', '1C'],
                ['Estructuras I', '2C'],
                ['Historia de la Arquitectura II', '2C'],
                ['Instalaciones Edilicias I', 'Anual'],
            ],
            3 => [
                ['Diseño Arquitectónico II', '1C'],
                ['Estructuras II', '1C'],
                ['Urbanismo I', '2C'],
                ['Historia de la Arquitectura III', '2C'],
                ['Instalaciones Edilicias II', 'Anual'],
            ],
            4 => [
                ['Diseño Arquitectónico III', '1C'],
                ['Estructuras III', '2C'],
                ['Urbanismo II', '2C'],
                ['Gestión de Obras', 'Anual'],
                ['Taller de Proyecto I', '2C'],
            ],
            5 => [
                ['Práctica Profesional Supervisada', 'Anual'],
                ['Taller de Proyecto Final', '2C'],
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
            ['Matemática II', 'Matemática I'],
            ['Estructuras I', 'Matemática II'],
            ['Diseño Arquitectónico I', 'Introducción a la Arquitectura'],
            ['Estructuras II', 'Estructuras I'],
            ['Diseño Arquitectónico II', 'Diseño Arquitectónico I'],
            ['Urbanismo I', 'Historia de la Arquitectura II'],
            ['Estructuras III', 'Estructuras II'],
            ['Diseño Arquitectónico III', 'Diseño Arquitectónico II'],
            ['Urbanismo II', 'Urbanismo I'],
            ['Taller de Proyecto I', 'Gestión de Obras'],
            ['Taller de Proyecto Final', 'Taller de Proyecto I'],
            ['Práctica Profesional Supervisada', 'Diseño Arquitectónico III'],
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
