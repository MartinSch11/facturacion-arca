<?php

namespace App\Services;

use App\Models\Boleta;
use App\Models\Configuracion;
use App\Models\DetalleFactura;
use App\Models\Factura;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PagoBoletaService
{
    public function __construct(
        protected AfipService $afipService,
        protected PdfFacturaService $pdfFacturaService,
    ) {
    }

    // Procesa el pago de una boleta y genera la factura correspondiente usando WS de AFIP.
    public function procesarPago(Boleta $boleta, string $formaPago, string $tipoFactura = 'C'): void
    {
        $config = Configuracion::firstOrFail();
        $fecha = now();

        $alumno = $boleta->carreraXAlumno?->alumno;
        if (!$alumno) {
            throw new Exception("Alumno no encontrado para la boleta.");
        }

        $importeTotal = $boleta->importe_pagado;
        if ($importeTotal <= 0) {
            throw new Exception("El importe de la boleta es inválido.");
        }

        $tipoComprobante = $this->getTipoComprobante($tipoFactura);
        [$tipoDoc, $nroDoc, $condicionIVA] = $this->determinarDocumentoYCondicionIVA($alumno, $tipoFactura, $config->cuit);
        [$importeNeto, $importeIVA] = $this->calcularImportes($importeTotal, $tipoFactura);

        $data = $this->obtenerDatosFactura(
            $alumno,
            $boleta,
            $fecha,
            $tipoComprobante,
            $tipoDoc,
            $nroDoc,
            $condicionIVA,
            $formaPago,
            $importeTotal,
            $importeNeto,
            $importeIVA,
            $tipoFactura
        );

        $factura = Factura::create([
            'matricula' => $boleta->matricula,
            'fecha_emision' => $fecha,
            'fecha_vencimiento' => $fecha->copy()->addDays(15),
            'importe_pagado' => $importeTotal,
            'fecha_pago' => $fecha,
            'forma_pago' => $formaPago,
            'tipo_factura' => $tipoFactura,
            'estado' => 'pendiente',
            'datos_para_afip' => json_encode($data),
        ]);

        Log::debug('Datos para AFIP generados:', $data);
        
        DetalleFactura::create([
            'nro_boleta' => $boleta->nro_boleta,
            'nro_factura' => $factura->nro_factura,
        ]);

        $boleta->update([
            'estado' => 'pagado',
            'fecha_pago' => $fecha,
        ]);
    }

    // Retorna el tipo de comprobante AFIP correspondiente al tipo de factura.
    private function getTipoComprobante(string $tipo): int
    {
        return match ($tipo) {
            'A' => 1,
            'B' => 6,
            default => 11,
        };
    }

    // Determina el tipo de documento, número y condición IVA del receptor.
    private function determinarDocumentoYCondicionIVA($alumno, string $tipoFactura, string $cuit): array
    {
        if (in_array($tipoFactura, ['A', 'B'])) {
            if (empty($alumno->cuit)) {
                throw new Exception("El alumno no tiene CUIT y no puede recibir facturas tipo $tipoFactura.");
            }

            $tipoDoc = 80;
            $nroDoc = preg_replace('/[^\d]/', '', $alumno->cuit);

            $condiciones = $this->afipService->obtenerCondicionesIvaReceptor($cuit);
            $clase = $tipoFactura;

            foreach ($condiciones as $id => $condicion) {
                if (in_array($clase, explode('/', $condicion->Cmp_Clase))) {
                    return [$tipoDoc, $nroDoc, $id];
                }
            }

            throw new Exception("No se encontró una condición IVA válida para el comprobante tipo $tipoFactura.");
        }

        return [96, $alumno->dni, 5]; // Default: DNI y Consumidor Final
    }

    //Calcula el importe neto y el IVA según el tipo de factura.
    private function calcularImportes(float $total, string $tipo): array
    {
        if (in_array($tipo, ['A', 'B'])) {
            $neto = round($total / 1.21, 2);
            return [$neto, round($total - $neto, 2)];
        }

        return [$total, 0];
    }

    // Arma los datos necesarios para enviar a AFIP y generar la factura electrónica.
    private function obtenerDatosFactura($alumno, Boleta $boleta, Carbon $fecha, int $tipoComprobante, int $tipoDoc, string $nroDoc, int $condicionIVA, string $formaPago, float $importeTotal, float $importeNeto, float $importeIVA, string $tipoFactura): array
    {
        $conceptoBase = $boleta->concepto?->nombre ?? 'Sin concepto';
        $conceptoMes = ucfirst($fecha->translatedFormat('F Y'));

        $data = [
            'tipoComprobante' => $tipoComprobante,
            'concepto' => 1,
            'tipoDoc' => $tipoDoc,
            'nroDoc' => $nroDoc,
            'nombreApellido' => "{$alumno->nombre} {$alumno->apellido}",
            'domicilio' => $alumno->domicilio ?? 'Sin domicilio',
            'condicionIVAReceptorId' => $condicionIVA,
            'formaPago' => $formaPago,
            'fechaComprobante' => $fecha->format('Ymd'),

            'impTotal' => $importeTotal,
            'impTotConc' => 0,
            'impNeto' => $importeNeto,
            'impOpEx' => 0,
            'impTrib' => 0,
            'impIVA' => $importeIVA,

            'monId' => 'PES',
            'monCotiz' => 1,

            'periodo_desde' => $fecha->copy()->startOfMonth()->format('d/m/Y'),
            'periodo_hasta' => $fecha->copy()->endOfMonth()->format('d/m/Y'),
            'vto_pago' => $fecha->copy()->addDays(15)->format('d/m/Y'),
            'concepto-boleta' => $conceptoBase,
            'concepto_nombre' => "$conceptoBase - $conceptoMes",
        ];

        if (in_array($tipoFactura, ['A', 'B']) && $importeIVA > 0) {
            $data['iva'] = [
                [
                    'Id' => 5, // 21%
                    'BaseImp' => $importeNeto,
                    'Importe' => $importeIVA,
                ]
            ];
        }

        return $data;
    }

    // Valida la respuesta de AFIP y registra en el log cualquier error u observación. 
    private function validarRespuestaAfip(object $result): void
    {
        $detalle = $result->FECAESolicitarResult->FeDetResp->FECAEDetResponse ?? null;

        if (empty($detalle?->CAE) || empty($detalle?->CAEFchVto)) {
            $errores = $result->FECAESolicitarResult->Errors->Err ?? [];
            $observaciones = $detalle->Observaciones->Obs ?? [];
            $mensaje = '';

            foreach ((array) $errores as $err) {
                $mensaje .= isset($err->Code) ? "[{$err->Code}] {$err->Msg} " : json_encode($err) . ' ';
            }

            foreach ((array) $observaciones as $obs) {
                $mensaje .= isset($obs->Code) ? "[Obs {$obs->Code}] {$obs->Msg} " : json_encode($obs) . ' ';
            }

            // Log detallado
            Log::error('Error al obtener CAE desde AFIP', [
                'mensaje' => $mensaje,
                'afip_response' => $result,
            ]);

            // Lanza una excepción para el usuario
            throw new Exception("Ocurrió un error al generar la factura. Por favor, comunicate con soporte.");
        }
    }

}
