<?php
/**
 * Fichier générant la commande portail:old-to-new.
 * Télécharge toutes les données dans l'ancien Portail vers celui-ci.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OldToNew extends Command
{
    /**
     * @var string
     */
    protected $signature = 'portail:old-to-new';

    /**
     * @var string
     */
    protected $description = 'Download all data from the old Portail';

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
        $this->call('portail:clear');
    }
}
