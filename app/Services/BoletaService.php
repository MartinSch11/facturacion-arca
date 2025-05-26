<?php

namespace App\Services;

use App\Models\Boleta;
use App\Models\CarreraXAlumno;
use App\Models\Concepto;
use App\Models\EmisionBoleta;
use App\Models\Precio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BoletaService
{
    public function generarBoletas(array $data): int
    {
        return DB::transaction(function () use ($data) {
            $ultimoBoleta = Boleta::max('nro_boleta');
            $proximoNroBoleta = $ultimoBoleta ? intval($ultimoBoleta) + 1 : 1;
            $boletaInicial = str_pad($proximoNroBoleta, 6, '0', STR_PAD_LEFT);

            $emision = EmisionBoleta::create([
                'fecha_primer_vencimiento' => $data['fecha_primer_vencimiento'],
                'fecha_segundo_vencimiento' => $data['fecha_segundo_vencimiento'] ?? null,
                'fecha_tercer_vencimiento' => $data['fecha_tercer_vencimiento'] ?? null,
                'boleta_inicial' => $boletaInicial,
                'boleta_final' => 0, // provisional
            ]);

            $excludedConditions = ['Regular Inactivo', 'Baja'];
            $boletasCreadas = 0;
            $boletaFinal = null;

            foreach ($data['id_carreras'] as $carreraId) {
                $precio = Precio::where('id_carrera', $carreraId)->value('precio') ?? 0;
                if ($precio <= 0) {
                    continue;
                }

                $alumnos = CarreraXAlumno::where('id_carrera', $carreraId)
                    ->whereNotIn('id_condicion', function ($query) use ($excludedConditions) {
                        $query->select('id_condicion')
                            ->from('condiciones')
                            ->whereIn('nombre', $excludedConditions);
                    })
                    ->get();

                foreach ($alumnos as $alumno) {
                    $nroBoleta = $proximoNroBoleta++;
                    $nroBoletaFormateado = str_pad($nroBoleta, 6, '0', STR_PAD_LEFT);

                    $nombreConcepto = Concepto::find($data['id_concepto'])->nombre;
                    $mes = Carbon::parse($data['fecha_primer_vencimiento'])->translatedFormat('F Y');
                    $nombreDetallado = "{$nombreConcepto} de {$mes}";

                    Boleta::create([
                        'nro_boleta' => $nroBoletaFormateado,
                        'matricula' => $alumno->matricula,
                        'id_concepto' => $data['id_concepto'],
                        'nombre_detallado' => $nombreDetallado,
                        'estado' => 'pendiente',
                        'fecha_pago' => null,
                        'importe_pagado' => $precio,
                        'id_emision' => $emision->id_emision,
                    ]);

                    $boletaFinal = $nroBoletaFormateado;
                    $boletasCreadas++;
                }
            }

            if ($boletasCreadas > 0) {
                $emision->update([
                    'boleta_final' => $boletaFinal,
                ]);
            }

            return $boletasCreadas;
        });
    }
}
