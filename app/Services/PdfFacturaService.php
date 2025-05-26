<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PdfFacturaService
{
    public function generarStream(array $data, string $comprobanteFormateado, string $cae, string $caeVto): string
    {
        $html = view('facturaC', [
            'data' => $data,
            'cae' => $cae,
            'caeVto' => $caeVto,
            'comprobanteFormateado' => $comprobanteFormateado,
        ])->render();

        $response = Http::withBasicAuth('api', 'sk_c9090c87416d6caa7d7207850c258e1fb4b8e023')
            ->post('https://api.pdfshift.io/v3/convert/pdf', [
                'source' => $html,
                'landscape' => false,
                'format' => 'A4',
                'margin' => '10mm',
            ]);

        if (! $response->successful()) {
            throw new \Exception('Error al generar PDF: ' . $response->body());
        }

        return $response->body(); // Retorna el PDF como contenido crudo
    }
}
