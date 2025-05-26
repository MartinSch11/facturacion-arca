<?php

namespace App\Helpers;
class AfipQrHelper
{
    public static function generarUrlQr(array $data): string
    {
        $json = [
            'ver'        => 1,
            'fecha'      => $data['fechaComprobante'],
            'cuit'       => $data['cuit'],
            'ptoVta'     => 1,
            'tipoCmp'    => 11,
            'nroCmp'     => (int) $data['nroFactura'],
            'importe'    => (float) number_format($data['impTotal'], 2, '.', ''),
            'moneda'     => 'PES',
            'ctz'        => 1,
            'tipoDocRec' => 96,
            'nroDocRec'  => $data['nroDoc'],
            'tipoCodAut' => 'E',
            'codAut'     => $data['cae'],
        ];

        $jsonStr = json_encode($json, JSON_UNESCAPED_SLASHES);
        $base64 = base64_encode($jsonStr);

        return 'https://www.afip.gob.ar/fe/qr/?p=' . $base64;
    }
}
