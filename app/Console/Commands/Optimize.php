<?php
/**
 * Fichier générant la commande quick:optimize.
 * Optimise le cache de l'application.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Optimize extends Command
{
    /**
     * @var string
     */
    protected $signature = 'quick:optimize';

    /**
     * @var string
     */
    protected $description = 'Optimize the application by caching resources';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $this->call('view:cache');
        $this->call('route:cache');
        $this->call('config:clear');
        $this->call('config:cache');
    }
}
