<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jetons de session expirés et liens de réinitialisation périmés.
        // Nécessite la tâche cron `* * * * * php artisan schedule:run` sur le serveur.
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
