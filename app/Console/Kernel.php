<?php
/**
 * Files generating console and its commands.
 * Enables console and its commands start.
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
     * Command list.
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
     * Application's Cron.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->hourly();
    }

    /**
     * Saves application commands.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
