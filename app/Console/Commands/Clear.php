<?php
/**
 * Fichier générant la commande quick:clear.
 * Efface le cache de l'application.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * @var string
     */
    protected $signature = 'quick:clear';

    /**
     * @var string
     */
    protected $description = 'Clear all compiled and cached resources';

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
        shell_exec('composer dump-autoload');

        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('auth:clear-resets');
        $this->call('cache:clear');
        $this->call('clear-compiled');
        $this->call('clear');

        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
    }
}
