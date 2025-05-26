<?php

namespace App\Services;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Log;

class AfipService
{
    protected $tokenService;
    protected $facturacionService;
    protected $comprobanteService;
    protected $wsfe;

    public function __construct(
        TokenService $tokenService,
        FacturacionService $facturacionService,
        ComprobanteService $comprobanteService
    ) {
        $this->tokenService = $tokenService;
        $this->facturacionService = $facturacionService;
        $this->comprobanteService = $comprobanteService;

        // Inicializa el cliente SOAP para el servicio WSFE de AFIP
        $this->wsfe = new \SoapClient(storage_path('app/afip/wsfe.wsdl'), [
            'soap_version' => SOAP_1_2,
            'location' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
            'trace' => 1,
            'exceptions' => 1,
        ]);
    }

    // Recibe los datos, asigna numeración, envía a AFIP y retorna la respuesta con el número final del comprobante.
    public function facturar(array $data): object
    {
        $configuracion = $this->obtenerConfiguracion();

        $data['puntoDeVenta'] = $configuracion->punto_venta;
        $data['cuit'] = $configuracion->cuit;

        if (!isset($data['tipoComprobante'])) {
            throw new \Exception("El tipo de comprobante no está definido.");
        }

        $nuevoNumero = $this->obtenerNuevoNumeroComprobante($data['puntoDeVenta'], $data['tipoComprobante'], $data['cuit']);
        $data = $this->agregarNumeracionAFactura($data, $nuevoNumero);

        $auth = $this->tokenService->getTokenAndSign();
        $results = $this->facturacionService->facturar($data, $auth);

        $this->comprobanteService->actualizarUltimoNumero($data['puntoDeVenta'], $nuevoNumero);

        $results->comprobanteFinal = $this->formatearComprobante($data['puntoDeVenta'], $nuevoNumero);

        return $results;
    }

    // Obtiene la configuración general del sistema (CUIT y punto de venta).
    private function obtenerConfiguracion(): Configuracion
    {
        $configuracion = Configuracion::first();
        if (!$configuracion) {
            throw new \Exception("No se encontró la configuración en la base de datos.");
        }
        return $configuracion;
    }


    // Calcula el próximo número de comprobante, consultando el último autorizado por AFIP.
    private function obtenerNuevoNumeroComprobante(int $puntoVenta, int $tipoComprobante, string $CUIT): int
    {
        $ultimoAfip = $this->obtenerUltimoNumeroDesdeAfip($puntoVenta, $tipoComprobante, $CUIT);
        return $ultimoAfip + 1;
    }

    // Agrega los datos de numeración al array de factura.
    private function agregarNumeracionAFactura(array $data, int $numero): array
    {
        $data['cbteDesde'] = $numero;
        $data['cbteHasta'] = $numero;
        return $data;
    }

    // Formatea el número de comprobante final (ej: 0001-00000001).
    private function formatearComprobante(int $puntoVenta, int $numero): string
    {
        $ptoVta = str_pad($puntoVenta, 4, '0', STR_PAD_LEFT);
        $nroCbte = str_pad($numero, 8, '0', STR_PAD_LEFT);
        return "{$ptoVta}-{$nroCbte}";
    }

    //Consulta a AFIP el último número de comprobante autorizado para un punto de venta y tipo de comprobante.
    public function obtenerUltimoNumeroDesdeAfip(int $puntoVenta, int $tipoComprobante, string $CUIT): int
    {
        $auth = $this->tokenService->getTokenAndSign();

        try {
            $client = new \SoapClient(storage_path('app/afip/wsfe.wsdl'), [
                'soap_version' => SOAP_1_2,
                'location' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
                'trace' => 1,
                'exceptions' => 1,
            ]);

            $response = $client->FECompUltimoAutorizado([
                'Auth' => [
                    'Token' => $auth['token'],
                    'Sign' => $auth['sign'],
                    'Cuit' => $CUIT,
                ],
                'PtoVta' => $puntoVenta,
                'CbteTipo' => $tipoComprobante,
            ]);

            Log::debug('Último comprobante autorizado AFIP:', (array) $response);
            return $response->FECompUltimoAutorizadoResult->CbteNro;
        } catch (\SoapFault $e) {
            throw new \Exception("Error consultando último número AFIP: " . $e->getMessage());
        }
    }

    // Consulta a AFIP las condiciones de IVA posibles para un receptor dado su CUIT.
    public function obtenerCondicionesIvaReceptor(string $CUIT): array
    {
        $auth = $this->tokenService->getTokenAndSign();

        try {
            $response = $this->wsfe->FEParamGetCondicionIvaReceptor([
                'Auth' => [
                    'Token' => $auth['token'],
                    'Sign' => $auth['sign'],
                    'Cuit' => $CUIT,
                ],
            ]);

            if (
                isset($response->FEParamGetCondicionIvaReceptorResult?->ResultGet?->CondicionIvaReceptor)
            ) {
                $condiciones = $response->FEParamGetCondicionIvaReceptorResult->ResultGet->CondicionIvaReceptor;
                $condicionesArray = [];

                foreach ($condiciones as $condicion) {
                    $condicionesArray[$condicion->Id] = $condicion;
                }

                return $condicionesArray;
            }

            Log::warning('No se encontraron condiciones IVA válidas en la respuesta AFIP.', (array) $response);
            return [];
        } catch (\SoapFault $e) {
            throw new \Exception("Error consultando condiciones IVA AFIP: " . $e->getMessage());
        }
    }
}
