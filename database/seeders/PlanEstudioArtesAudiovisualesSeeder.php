<?php

namespace Database\Seeders;

use App\Models\ComisionMateria;
use App\Models\Materia;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanEstudioArtesAudiovisualesSeeder extends Seeder
{
    public function run(): void
    {
        $plan = PlanEstudio::create([
            'id_carrera' => 13, // Ajusta el ID según corresponda en tu tabla carreras
            'nombre' => 'Plan 2024 - Lic. en Artes Audiovisuales',
            'anio_implementacion' => 2024,
        ]);

        $materiasPorAnio = [
            1 => [
                ['Historia del Arte I', '1C'],
                ['Introducción a la Realización Audiovisual', '1C'],
                ['Guion I', '2C'],
                ['Fotografía', '2C'],
                ['Lenguaje Audiovisual', 'Anual'],
            ],
            2 => [
                ['Historia del Arte II', '1C'],
                ['Guion II', '1C'],
                ['Edición y Montaje', '2C'],
                ['Producción Audiovisual', '2C'],
                ['Teoría de la Imagen', 'Anual'],
            ],
            3 => [
                ['Dirección de Fotografía', '1C'],
                ['Sonido', '1C'],
                ['Dirección de Arte', '2C'],
                ['Animación', '2C'],
                ['Estética Cinematográfica', 'Anual'],
            ],
            4 => [
                ['Dirección de Actores', '1C'],
                ['Documental', '2C'],
                ['Seminario de Producción', 'Anual'],
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
                    'cupo' => 35,
                ]);
            }
        }

        $correlativas = [
            ['Historia del Arte II', 'Historia del Arte I'],
            ['Guion II', 'Guion I'],
            ['Edición y Montaje', 'Lenguaje Audiovisual'],
            ['Producción Audiovisual', 'Introducción a la Realización Audiovisual'],
            ['Dirección de Fotografía', 'Fotografía'],
            ['Dirección de Arte', 'Estética Cinematográfica'],
            ['Taller de Proyecto I', 'Seminario de Producción'],
            ['Taller de Proyecto Final', 'Taller de Proyecto I'],
            ['Práctica Profesional Supervisada', 'Dirección de Actores'],
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
