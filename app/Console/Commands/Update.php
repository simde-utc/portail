<?php
/**
 * Files generating the command: portail:update.
 * Update the application to the next version after a git pull.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Update extends Command
{
    /**
     * @var string
     */
    protected $signature = 'portail:update';

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
     * Command execution.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(7);
        $bar->advance();

        // Clear cache.
        $this->info(' [Portail Update] Préparation');
        $this->call('portail:clear');
        $bar->advance();

        // Updating.
        $this->info(' [Portail Update] Mise à jour des dépendances Composer');
        shell_exec('composer update');
        $bar->advance();

        $this->info(' [Portail Update]Mise à jour des dépendances NodeJS');
        shell_exec('npm update');
        shell_exec('npm run dev');
        $bar->advance();

        // Clear cache.
        $this->info(' [Portail Update] Nettoyage');
        $this->call('portail:clear');
        $bar->advance();

        // Migration.
        $this->info(' [Portail Update] Migration');
        if ($this->confirm('Supprimer la base de données ?')) {
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        if ($this->confirm('Remplir la base de données ?')) {
            $this->call("db:seed");
        }

        $bar->advance();

        // Optimisation.
        $this->info(' [Portail Update] Optimisation');
        $this->call('portail:optimize');
        $bar->advance();

        // End.
        $bar->finish();
        $this->info(' [Portail Update] Installation finie !');
    }
}
