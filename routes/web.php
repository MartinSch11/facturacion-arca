<?php

use App\Filament\Alumno\Pages\PagoBoleta;
use App\Helpers\NumeroALetras;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;
use App\Models\Condicion;
use App\Models\Factura;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/descargar-factura/{nroFactura}', [FacturaController::class, 'descargar'])
    ->middleware('auth')
    ->name('descargar-factura');

// Ruta que muestra la vista previa de una factura tipo C con los datos completos del alumno y su comisión
Route::get('/factura-c/{nroFactura}', function ($nroFactura) {
    Carbon::setLocale('es');

    // Busca la factura y carga relaciones necesarias
    $factura = Factura::with([
        'detalleFactura.boleta.carreraXAlumno.alumno.provincia',
        'detalleFactura.boleta.carreraXAlumno.alumno.localidad',
        'detalleFactura.boleta.carreraXAlumno.alumno',
        'detalleFactura.boleta.concepto',
        'detalleFactura.boleta.emisionBoleta',
    ])->findOrFail($nroFactura);

    // Obtiene la boleta y el alumno relacionados a la factura
    $boleta = $factura->detalleFactura->boleta;
    $alumno = $boleta->carreraXAlumno->alumno;

    // Obtiene la comisión más reciente del alumno
    $comision = $alumno->comisiones()->latest('id')->first();

    if ($comision) {
        // Busca la cursada del alumno para esa comisión y toma el año
        $cursada = $alumno->cursadas()->where('id_comision_materia', $comision->id)->first();
        if ($cursada) {
            $anio = $cursada->anio;
        }
    }

    // Arma el código de comisión con modalidad (3 si es anual)
    if ($comision && $comision->materia) {
        $modalidad = strtoupper($comision->materia->modalidad);
        $modalidad = $modalidad === 'ANUAL' ? '3' : $modalidad;
        $codigoComision = sprintf(
            'D-%s%s.%s-%s',
            $anio,
            strtoupper($comision->division),
            strtoupper($comision->turno),
            $modalidad
        );
    } else {
        $codigoComision = 'Sin comisión';
    }

    // Obtiene la fecha de la cuota y arma los datos para la vista
    $fechaCuota = Carbon::parse($boleta->emisionBoleta?->fecha_primer_vencimiento);
    $tipo = strtoupper($factura->tipo_factura);
    $codigoFactura = match ($tipo) {
        'A' => '001',
        'B' => '006',
        'C' => '011',
        default => '000',
    };

    // Prepara los datos para la vista de la factura
    $data = [
        'tipo_factura' => $tipo,
        'cod_factura' => $codigoFactura,
        'fechaComprobante' => $factura->fecha_emision->format('d/m/Y'),
        'cuit' => '23442802009',
        'nroDoc' => $alumno->dni,
        'localidad' => $alumno->localidad?->name ?? 'Sin localidad',
        'provincia' => $alumno->provincia?->name ?? 'Sin provincia',
        'nombreApellido' => $alumno->nombre . ' ' . $alumno->apellido,
        'domicilio' => $alumno->direccion ?? 'Sin domicilio',
        'codigoCurso' => $codigoComision,
        'periodo_desde' => $boleta->periodo_desde,
        'periodo_hasta' => $boleta->periodo_hasta,
        'vto_pago' => $factura->fecha_vencimiento->format('d/m/Y'),
        'concepto_nombre' => $boleta->nombre_detallado,
        'impTotal' => $factura->importe_pagado,
        'formaPago' => $factura->forma_pago_label,
        'totalEnLetras' => NumeroALetras::convertir($factura->importe_pagado),
        'mes_numero' => $fechaCuota->month,
        'mes_nombre' => mb_strtoupper($fechaCuota->translatedFormat('F')),
    ];

    // Formatea el número de comprobante y obtiene datos de CAE
    $comprobanteFormateado = sprintf('%04d-%08d', 1, $factura->nro_factura);
    $cae = $factura->cae;
    $caeVto = Carbon::parse($factura->cae_vencimiento)->format('d/m/Y');

    // Retorna la vista de la factura con los datos armados
    return view('facturaNuevaEscuela', compact('data', 'comprobanteFormateado', 'cae', 'caeVto'));
})->name('vista-factura-c');

// Ejemplo de uso de la ruta /factura-c/{nroFactura}:
// tenemos una factura con nro_factura = 2
// Puedes acceder desde el navegador a:
// http://127.0.0.1:8000/factura-c/2


