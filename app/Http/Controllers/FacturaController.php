<?php

namespace App\Http\Controllers;

use App\Helpers\AfipQrHelper;
use App\Models\Configuracion;
use App\Models\Factura;
use App\Services\PdfFacturaService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FacturaController extends Controller
{
    // Genera y descarga el PDF de una factura en base a su número.
    public function descargar(int $nroFactura, PdfFacturaService $pdf): Response
    {
        $factura = $this->obtenerFacturaConRelaciones($nroFactura);

        $data = $this->armarDatosFactura($factura);
        $data['urlQr'] = $this->generarUrlQrAfip($factura);
        $data['qrImage'] = $this->generarQrImageBase64($data['urlQr']);

        $comprobanteFormateado = $this->formatearComprobante($factura->nro_factura);

        $pdfContent = $pdf->generarStream(
            $data,
            $comprobanteFormateado,
            $factura->cae,
            $factura->cae_vencimiento
        );

        return $this->respuestaPdf($pdfContent, $comprobanteFormateado);
    }

    // Obtiene la factura con las relaciones necesarias.
    private function obtenerFacturaConRelaciones(int $nroFactura): Factura
    {
        return Factura::with([
            'detalleFactura.boleta.carreraXAlumno.alumno',
            'detalleFactura.boleta.concepto',
        ])->findOrFail($nroFactura);
    }

    // Construye el array de datos necesarios para el PDF de la factura.
    private function armarDatosFactura(Factura $factura): array
    {
        $configuracion = Configuracion::firstOrFail();
        $boleta = $factura->detalleFactura->boleta;
        $emisionboleta = $boleta->emisionBoleta;
        $alumno = $boleta->carreraXAlumno->alumno;
        $fecha = Carbon::parse($emisionboleta->fecha_primer_vencimiento);

        return [
            'fechaComprobante' => $factura->fecha_emision->format('d/m/Y'),
            'cuit' => $configuracion->cuit,
            'nroDoc' => $alumno->dni,
            'nombreApellido' => $alumno->nombre . ' ' . $alumno->apellido,
            'domicilio' => $alumno->direccion ?? 'Sin domicilio',
            'periodo_desde' => $fecha->startOfMonth()->format('d/m/Y'),
            'periodo_hasta' => $fecha->endOfMonth()->format('d/m/Y'),
            'vto_pago' => $factura->fecha_vencimiento->format('d/m/Y'),
            'concepto_nombre' => $boleta->nombre_detallado,
            'impTotal' => $factura->importe_pagado,
            'formaPago' => $factura->forma_pago_label,
            'cae' => $factura->cae,
            'nroFactura' => $factura->nro_factura,
            'tipo_factura' => $factura->tipo_factura ?? 'C',
        ];
    }

    // Genera la URL para el código QR exigido por AFIP.
    private function generarUrlQrAfip(Factura $factura): string
    {
        $alumno = $factura->detalleFactura->boleta->carreraXAlumno->alumno;

        return AfipQrHelper::generarUrlQr([
            'fechaComprobante' => $factura->fecha_emision->format('Y-m-d'),
            'cuit' => '23442802009',
            'nroDoc' => $alumno->dni,
            'impTotal' => $factura->importe_pagado,
            'cae' => $factura->cae,
            'nroFactura' => $factura->nro_factura,
        ]);
    }

    // Genera el código QR en formato base64 para incrustar en el PDF.
    private function generarQrImageBase64(string $urlQr): string
    {
        return base64_encode(
            QrCode::format('png')->size(150)->generate($urlQr)
        );
    }

    // Devuelve el número de comprobante formateado (ej: 0001-00000012).
    private function formatearComprobante(int $nroFactura): string
    {
        return sprintf('%04d-%08d', 1, $nroFactura);
    }

    // Devuelve la respuesta HTTP con el PDF generado.
    private function respuestaPdf(string $pdfContent, string $nombreArchivo): Response
    {
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="FACTURA-' . $nombreArchivo . '.pdf"',
        ]);
    }
}
