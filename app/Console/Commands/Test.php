<?php
/**
 * Fichier générant la commande quick:test.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Lance les tests suffisants pour pouvoir merge dans develop.
 */
class Test extends Command
{
    /**
     * @var string
     */
    protected $signature = 'quick:test {file*}';

    /**
     * @var string
     */
    protected $description = 'Teste le code avant de pouvoir push le code';

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
        $this->file = implode($this->argument('file'), ' ');

        if ($this->runPHPCS()) {
            $this->output->error('Des erreurs ont été rencontrées lors de la vérification du linting');

            $value = $this->choice('Tenter de fixer les erreurs ?', ['Oui', 'Non'], 1);

            if ($value === 'Oui') {
                $this->runPHPCBF();

                if ($this->runPHPCS()) {
                    $this->output->error('Des erreurs n\'ont pas pu être corrigées lors de la vérification du linting');

                    exit(1);
                }
            } else {
                exit(1);
            }
        }

        $this->runPHPUnit();

        $this->output->success('Code parfait √');
    }

    /**
     * Lance le PHP Code Sniffer pour vérifier le style PHP
     *
     * @return integer
     */
    private function runPHPCS()
    {
        return $this->process(
            "./vendor/bin/phpcs ".$this->file
        );
    }

    /**
     * Lance le PHP Code Beautifer and Fixer pour corriger à la volée les problèmes de styles
     *
     * @return integer
     */
    private function runPHPCBF()
    {
        return $this->process(
            "./vendor/bin/phpcbf ".$this->file
        );
    }

    /**
     * Lance le PHP Unit pour tester que le code n'a pas cassé
     *
     * @return integer
     */
    private function runPHPUnit()
    {
        return $this->process(
            "./vendor/bin/phpunit"
        );
    }

    /**
     * Lance une commande bash
     *
     * @param string $command Commande à lancer
     * @return Process
     */
    private function process(string $command)
    {
        $process = new Process($command);

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        return $process->getExitCode();
    }
}
