<?php

namespace App\Jobs;

use App\Models\Factura;
use App\Services\AfipService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class EnviarFacturaAfip implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Ejecuta el envío de todas las facturas pendientes a AFIP.
     */
    public function handle()
    {
        $facturasPendientes = Factura::where('estado', 'pendiente')->get();
    
        if ($facturasPendientes->isEmpty()) {
            Log::info('[AFIP Job] No hay facturas pendientes para procesar.');
            return;
        }
    
        Log::info('[AFIP Job] Procesando ' . $facturasPendientes->count() . ' factura(s) pendiente(s).');
    
        foreach ($facturasPendientes as $factura) {
            try {
                $data = json_decode($factura->datos_para_afip, true);
    
                /** @var AfipService $afip */
                $afip = app(AfipService::class);
                $resultado = $afip->facturar($data);
    
                $detalle = $resultado->FECAESolicitarResult->FeDetResp->FECAEDetResponse ?? null;
                $cae = $detalle?->CAE ?? null;
                $caeVto = $detalle?->CAEFchVto ?? null;
    
                if ($cae && $caeVto) {
                    $factura->update([
                        'cae' => $cae,
                        'cae_vencimiento' => Carbon::createFromFormat('Ymd', $caeVto)->format('Y-m-d'),
                        'estado' => 'procesada',
                    ]);
    
                    Log::info("Factura {$factura->nro_factura} procesada correctamente.");
                } else {
                    $factura->update(['estado' => 'error']);
                    Log::warning("Factura {$factura->nro_factura} no obtuvo CAE.");
                }
    
            } catch (Throwable $e) {
                Log::error("Error facturando factura {$factura->nro_factura}: " . $e->getMessage());
                $factura->update(['estado' => 'error']);
            }
        }
    }
    
}
