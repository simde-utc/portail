<?php
/**
 * Fichier générant la commande quick:update.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Met à jour l'application vers la version suivante après un git pull.
 */
class Update extends Command
{
    /**
     * @var string
     */
    protected $signature = 'quick:update';

    /**
     * @var string
     */
    protected $description = 'Update the whole application';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exécution de la commande.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(7);
        $bar->advance();

        // Nettoyage du cache.
        $this->info(' [Quick Update] Cleaning');
        $this->call('quick:clear');
        $bar->advance();

        // Mise à jour.
        $this->info(' [Quick Update] Updating Composer dependencies');
        shell_exec('composer update');
        $bar->advance();

        $this->info(' [Quick Update] Updating Node dependencies');
        shell_exec('npm update');
        shell_exec('npm run dev');
        $bar->advance();

        // Nettoyage du cache.
        $this->info(' [Quick Update] Cleaning');
        $this->call('quick:clear');
        $bar->advance();

        // Migration.
        $this->info(' [Quick Update] Migrating');
        if ($this->confirm('Erase the database ?')) {
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        if ($this->confirm('Seed the database ?')) {
            $this->call("db:seed");
        }

        $bar->advance();

        // Optimisation.
        $this->info(' [Quick Update] Optimizing');
        $this->call('quick:optimize');
        $bar->advance();

        // Fin.
        $bar->finish();
        $this->info(' [Quick Update] Installation finished !');
    }
}
