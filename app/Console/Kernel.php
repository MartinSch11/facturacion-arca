<?php

namespace App\Console;

use App\Jobs\EnviarFacturaAfip;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function schedule(Schedule $schedule): void
    {
        // ✔️ Mensaje de log dentro de la tarea programada
        $schedule->call(function () {
            Log::info('[Scheduler] Tarea de prueba ejecutada.');
        })->everyMinute();

        // 💤 Este está comentado, activalo si querés ejecutar el Job directamente
        $schedule->job(new EnviarFacturaAfip)
            ->everyTenMinutes()
            ->before(function () {
                Log::info('[Scheduler] Ejecutando EnviarFacturaAfip');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

}