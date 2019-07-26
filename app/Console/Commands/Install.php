<?php
/**
 * File generating the command: portail:install.
 * Install the application.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class Install extends Command
{
    /**
     * @var string
     */
    protected $signature = 'portail:install';

    /**
     * @var string
     */
    protected $description = 'Install the whole application';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Automatic change of the .env file.
     * @param  string $param Parameter key to change.
     * @param  string $value Value to assign.
     * @return void
     */
    protected function changeEnv(string $param, string $value)
    {
        $param = str_replace('.', '\\.', str_replace('/', '\\/', $param));
        $value = str_replace('.', '\\.', str_replace('/', '\\/', $value));

        shell_exec('sed -i "s/'.$param.'=.*$/'.$param.'='.$value.'/g" .env');
    }

    /**
     * Command execution.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(9);
        $bar->advance();

        $this->info(' [Portail Install] Préparation');
        $this->info(' [Portail Install] Préparation - Portail');
        $subBar = $this->output->createProgressBar(14);
        $subBar->advance();
        $editEnv = true;

        if (file_exists('.env')) {
            $this->info(' /!\ Le fichier .env existe déjà /!\\ ');

            if ($this->confirm('Le remplacer (ancienne version déplacée en .env.last) ?')) {
                shell_exec('cp .env .env.last');
            } else {
                $editEnv = false;
            }
        }

        if ($editEnv) {
            $this->editEnv($bar, $subBar);
        } else {
            $subBar->finish();
        }

        $bar->advance();

        // Installation.
        $this->info(' [Portail Install] Installation des dépendances Composer');
        shell_exec('composer install');
        $bar->advance();

        // Configuration.
        $this->info(' [Portail Install] Définition des clés');
        $this->call('key:generate');
        $this->call('passport:keys');
        $bar->advance();

        $this->info(' [Portail Install] Installation des dépendances NodeJS');
        shell_exec('npm install');
        shell_exec('npm run dev');
        $bar->advance();

        $this->info(' [Portail Install] Migration');
        if ($this->confirm('Supprimer la base de données ?')) {
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        if ($this->confirm('Remplir la base de données ?')) {
            $this->call("db:seed");

            if (config('app.env') === 'production' && $this->confirm('Ajouter les données de l\'ancien Portail ?')) {
                $this->call("portail:old-to-new");
            }
        }

        $bar->advance();

        // Cache clear.
        $this->info(' [Portail Install] Nettoyage');
        $this->call('portail:clear');
        $bar->advance();

        // Optimization.
        $this->info(' [Portail Install] Optimisation');
        $this->call('portail:optimize');
        $bar->advance();

        // End.
        $bar->finish();
        $this->info(' [Portail Install] Installation finie !');
    }

    /**
     * Allow changes within .env file.
     *
     * @param ProgressBar $bar
     * @param ProgressBar $subBar
     * @return void
     */
    private function editEnv(ProgressBar $bar, ProgressBar $subBar)
    {
        $value = $this->ask('App name ?', 'Portail des Associations');
        $this->changeEnv('APP_NAME', '"'.$value.'"');
        $subBar->advance();

        $value = $this->choice('Environment ?', ['develop', 'production'], 'develop');
        $this->changeEnv('APP_ENV', $value);
        $subBar->advance();

        $value = $this->choice('Debug mode ?', ['true', 'false'], ($value === 'develop' ? 'true' : 'false'));
        $this->changeEnv('APP_DEBUG', $value);
        $subBar->advance();

        $value = $this->ask('App url ?', 'http://localhost');
        $this->changeEnv('APP_URL', $value);
        $subBar->advance();

        $value = $this->ask('App asso ?', 'simde');
        $this->changeEnv('APP_ASSO', $value);
        $subBar->advance();

        $value = $this->ask('Ginger key ?', '');
        $this->changeEnv('GINGER_KEY', $value);
        $subBar->advance();

        $this->info(' [Portail Install] Préparation - Base de données');
        $subBar->advance();

        $value = $this->choice('Type ?', ['mysql', 'pgsql', 'sqlite'], 'mysql');
        $this->changeEnv('DB_CONNECTION', $value);
        $subBar->advance();

        $value = $this->ask('Host ?', '127.0.0.1');
        $this->changeEnv('DB_HOST', $value);
        $subBar->advance();

        $value = $this->choice('Port ?', ['3306', '5432'], '3306');
        $this->changeEnv('DB_PORT', $value);
        $subBar->advance();

        $value = $this->ask('Database ?', 'portail');
        $this->changeEnv('DB_DATABASE', $value);
        $subBar->advance();

        $value = $this->ask('Username ?', 'portail');
        $this->changeEnv('DB_USERNAME', $value);
        $subBar->advance();

        $value = $this->ask('Password ?', 'portail');
        $this->changeEnv('DB_PASSWORD', $value);
        $subBar->advance();
    }
}
