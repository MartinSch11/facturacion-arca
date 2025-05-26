<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;

class FacturacionService
{
    protected string $wsfeWsdl;
    protected string $wsfeUrl;
    protected SoapClient $client;

    public function __construct()
    {
        $this->wsfeWsdl = storage_path('app/afip/wsfe.wsdl');
        $this->wsfeUrl = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx';

        // Se inicializa y guarda el cliente SOAP
        $this->client = new SoapClient($this->wsfeWsdl, [
            'soap_version' => SOAP_1_2,
            'location' => $this->wsfeUrl,
            'trace' => 1,
            'exceptions' => 1,
        ]);
    }

    // Envía el comprobante a AFIP utilizando FECAESolicitar.
    public function facturar(array $data, array $auth)
    {
        try {
            // Armamos el detalle del comprobante
            $detalle = [
                'Concepto' => $data['concepto'],
                'DocTipo' => $data['tipoDoc'],
                'DocNro' => $data['nroDoc'],
                'CbteDesde' => $data['cbteDesde'],
                'CbteHasta' => $data['cbteHasta'],
                'CbteFch' => $data['fechaComprobante'],
                'ImpTotal' => $data['impTotal'],
                'ImpTotConc' => $data['impTotConc'],
                'ImpNeto' => $data['impNeto'],
                'ImpOpEx' => $data['impOpEx'],
                'ImpTrib' => $data['impTrib'],
                'ImpIVA' => $data['impIVA'],
                'MonId' => $data['monId'],
                'MonCotiz' => $data['monCotiz'],
                'CondicionIVAReceptorId' => $data['condicionIVAReceptorId'],
            ];

            // Agregamos IVA solo si corresponde (facturas A o B)
            if (isset($data['iva']) && $data['tipoComprobante'] !== 11) {
                $detalle['Iva'] = $data['iva'];
            }

            // Log de datos enviados
            Log::debug('Enviando datos a AFIP FECAESolicitar', [
                'FeCabReq' => [
                    'CantReg' => 1,
                    'PtoVta' => $data['puntoDeVenta'],
                    'CbteTipo' => $data['tipoComprobante'],
                ],
                'FeDetReq' => [
                    'FECAEDetRequest' => $detalle,
                ],
            ]);

            // Enviamos la solicitud al WS de AFIP
            return $this->client->FECAESolicitar([
                'Auth' => [
                    'Token' => $auth['token'],
                    'Sign' => $auth['sign'],
                    'Cuit' => $data['cuit'],
                ],
                'FeCAEReq' => [
                    'FeCabReq' => [
                        'CantReg' => 1,
                        'PtoVta' => $data['puntoDeVenta'],
                        'CbteTipo' => $data['tipoComprobante'],
                    ],
                    'FeDetReq' => [
                        'FECAEDetRequest' => $detalle,
                    ],
                ],
            ]);
        } catch (SoapFault $e) {
            // Capturamos errores del WS
            throw new \Exception("SOAP Fault: " . $e->getMessage());
        }
    }
}
