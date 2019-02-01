<?php
/**
 * Fichier générant la commande quick:install.
 * Installe l'application.
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
    protected $signature = 'quick:install';

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
     * Changement automatique du fichier .env
     * @param  string $param Paramètre clé à changer.
     * @param  string $value Valeur à assigner.
     * @return void
     */
    protected function changeEnv(string $param, string $value)
    {
        $param = str_replace('.', '\\.', str_replace('/', '\\/', $param));
        $value = str_replace('.', '\\.', str_replace('/', '\\/', $value));

        shell_exec('sed -i "s/'.$param.'=.*$/'.$param.'='.$value.'/g" .env');
    }

    /**
     * Exécution de la commande.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(9);
        $bar->advance();

        $this->info(' [Quick Install] Preparation');
        $this->info(' [Quick Install] Preparation - Portail');
        $subBar = $this->output->createProgressBar(14);
        $subBar->advance();
        $editEnv = true;

        if (file_exists('.env')) {
            $this->info(' /!\ An .env file already exists /!\\ ');

            if ($this->confirm('Edit over it ?')) {
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
        $this->info(' [Quick Install] Installing Composer dependencies');
        ini_set('memory_limit', '4G');
        shell_exec('composer install');
        $bar->advance();

        // Configuration.
        $this->info(' [Quick Install] Setting keys');
        $this->call('key:generate');
        $this->call('passport:keys');
        $bar->advance();

        $this->info(' [Quick Install] Installing Node dependencies');
        shell_exec('npm install');
        shell_exec('npm run dev');
        $bar->advance();

        // Migration.
        $this->info(' [Quick Install] Migrating');
        if ($this->confirm('Erase the database ?')) {
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        if ($this->confirm('Seed the database ?')) {
            $this->call("db:seed");
        }

        $bar->advance();

        // Effacement.
        $this->info(' [Quick Install] Cleaning');
        $this->call('quick:clear');
        $bar->advance();

        // Optimisation.
        $this->info(' [Quick Install] Optimizing');
        $this->call('quick:optimize');
        $bar->advance();

        // Fin.
        $bar->finish();
        $this->info(' [Quick Install] Installation finished !');
    }

    /**
     * Permet des changements de le fichier env
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

        $this->info(' [Quick Install] Preparation - Database');
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
