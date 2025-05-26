<?php

namespace App\Console\Commands;

use App\Jobs\EnviarFacturaAfip;
use Illuminate\Console\Command;

class ProcesarFacturasAfip extends Command
{
    protected $signature = 'factura:procesar';
    protected $description = 'Procesa todas las facturas pendientes enviándolas a AFIP inmediatamente.';

    public function handle(): int
    {
        $this->info('Procesando facturas pendientes...');

        // Ejecuta directamente el Job (sin pasar por la cola)
        EnviarFacturaAfip::dispatch();

        $this->info('Proceso de facturación completado.');

        return Command::SUCCESS;
    }
}
