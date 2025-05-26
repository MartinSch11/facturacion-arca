<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Boleta;
use App\Models\Configuracion;
use App\Models\DetalleFactura;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    public function formularioPago(Boleta $boleta)
    {
        return view('alumno.pago', compact('boleta'));
    }

    public function procesarPago(Request $request, Boleta $boleta)
    {
        $request->validate([
            'forma_pago' => 'required|in:contado,tarjeta_credito,tarjeta_debito',
        ]);

        $fechaHoy = Carbon::now()->format('Y-m-d');

        // Crear factura
        $factura = Factura::create([
            'matricula' => $boleta->matricula,
            'fecha_emision' => $fechaHoy,
            'fecha_vencimiento' => Carbon::now()->addDays(15),
            'importe_pagado' => $boleta->importe_pagado,
            'fecha_pago' => $fechaHoy,
            'cae' => Str::upper(Str::random(12)),
            'cae_vencimiento' => Carbon::now()->addDays(30),
        ]);

        // Crear detalle factura
        DetalleFactura::create([
            'nro_boleta' => $boleta->nro_boleta,
            'nro_factura' => $factura->nro_factura,
        ]);

        // Marcar boleta como pagada
        $boleta->estado = 'pagado';
        $boleta->fecha_pago = $fechaHoy;
        $boleta->save();

        return redirect()->route('ver.boleta', $boleta)->with('success', 'Pago registrado y factura emitida.');
    }
}
