<?php
/**
 * Fichier générant la console et ses commandes.
 * Permet le lancement de la console et de ses commandes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Liste des commandes.
     *
     * @var array
     */
    protected $commands = [
        Commands\Clear::class,
        Commands\Install::class,
        Commands\Optimize::class,
        Commands\Test::class,
        Commands\Update::class,
    ];

    /**
     * Cron de l'application.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Enregistre les commandes de l'applications.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
