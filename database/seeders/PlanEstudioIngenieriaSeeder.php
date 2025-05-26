<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PlanEstudio;
use App\Models\ComisionMateria;
use App\Models\Materia;

class PlanEstudioIngenieriaSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el plan de estudio
        $plan = PlanEstudio::create([
            'id_carrera' => 1, // Ingeniería en Informática
            'nombre' => 'Plan 2024 - Ingeniería en Informática',
            'anio_implementacion' => 2024,
        ]);

        // Lista de materias por año
        $materiasPorAnio = [
            1 => [
                ['Matemática I', '1C'],
                ['Algoritmos y Estructuras de Datos', '1C'],
                ['Arquitectura de Computadoras', '2C'],
                ['Matemática II', '2C'],
                ['Introducción a la Informática', 'Anual'],
            ],
            2 => [
                ['Sistemas Operativos', '1C'],
                ['Estructuras de Datos II', '1C'],
                ['Bases de Datos I', '2C'],
                ['Programación Orientada a Objetos', '2C'],
                ['Redes de Computadoras', 'Anual'],
            ],
            3 => [
                ['Bases de Datos II', '1C'],
                ['Ingeniería de Software I', '1C'],
                ['Seguridad Informática', '2C'],
                ['Inteligencia Artificial', '2C'],
                ['Teoría de la Computación', 'Anual'],
            ],
            4 => [
                ['Ingeniería de Software II', '1C'],
                ['Desarrollo Web Avanzado', '2C'],
                ['Arquitectura de Software', 'Anual'],
                ['Taller de Proyecto I', '2C'],
            ],
            5 => [
                ['Gestión de Proyectos', '1C'],
                ['Práctica Profesional Supervisada', 'Anual'],
                ['Taller de Proyecto Final', '2C'],
            ],
        ];

        $materiasCreadas = [];

        // Crear materias
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

        // Crear correlatividades básicas
        $correlativas = [
            ['Matemática II', 'Matemática I'],
            ['Estructuras de Datos II', 'Algoritmos y Estructuras de Datos'],
            ['Bases de Datos II', 'Bases de Datos I'],
            ['Ingeniería de Software II', 'Ingeniería de Software I'],
            ['Taller de Proyecto Final', 'Taller de Proyecto I'],
            ['Práctica Profesional Supervisada', 'Ingeniería de Software II'],
        ];

        foreach ($correlativas as [$materia, $requisito]) {
            DB::table('materias_correlativas')->insert([
                'id_materia' => $materiasCreadas[$materia]->id,
                'id_correlativa' => $materiasCreadas[$requisito]->id,
            ]);
        }

        // Crear una comisión por cada materia
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
