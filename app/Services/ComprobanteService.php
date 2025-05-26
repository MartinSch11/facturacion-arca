<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ComprobanteService
{
    public function obtenerUltimoNumero($puntoVenta)
    {
        $ultimoNumero = DB::table('ultimos_numeros')
            ->where('punto_venta', $puntoVenta)
            ->value('ultimo_numero');

        if ($ultimoNumero === null) {
            DB::table('ultimos_numeros')->insert([
                'punto_venta' => $puntoVenta,
                'ultimo_numero' => 0,
            ]);
            $ultimoNumero = 0;
        }

        return $ultimoNumero;
    }

    public function actualizarUltimoNumero($puntoVenta, $nuevoNumero)
    {
        DB::table('ultimos_numeros')
            ->where('punto_venta', $puntoVenta)
            ->update(['ultimo_numero' => $nuevoNumero]);
    }
}
