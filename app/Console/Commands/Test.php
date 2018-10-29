<?php
/**
 * Fichier générant la commande quick:test.
 * Lance les tests suffisants pour pouvoir merge dans develop.
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

class Test extends Command
{
    /**
     * @var string
     */
    protected $signature = 'quick:test {file?*}';

    /**
     * @var string
     */
    protected $description = 'Teste le code avant de pouvoir push le code';

    /**
     * Tous les dossiers à vérifier
     *
     * @var array
     */
    protected $dirs = [
        'app', 'bootstrap', 'config', 'database', 'resources/views', 'routes', 'tests',
    ];

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
        $this->files = $this->argument('file');
        $bar = $this->output->createProgressBar(5);

        $this->info(' [PHP Syntax] Vérification de la syntaxe PHP');
        $bar->advance();
        $this->info(PHP_EOL);

        if ($this->runPHPSyntax()) {
            $this->output->error('Des erreurs de syntaxe ont été détectées');

            return 1;
        }

        $this->info(' [PHP CS] Vérification du linting PHP');
        $bar->advance();
        $this->info(PHP_EOL);

        if ($this->runPHPCS()) {
            $this->output->error('Des erreurs ont été rencontrées lors de la vérification du linting');

            $value = $this->choice('Tenter de fixer les erreurs ?', ['Oui', 'Non'], 1);

            if ($value === 'Oui') {
                $this->runPHPCBF();

                if ($this->runPHPCS()) {
                    $this->output->error('Des erreurs n\'ont pas pu être corrigées lors de la vérification du linting');

                    return 1;
                }
            } else {
                return 1;
            }
        }

        $this->info(' [PHP MD] Vérification des optimisations PHP');
        $bar->advance();
        $this->info(PHP_EOL);

        if ($this->runPHPMD()) {
            $this->output->error('Des erreurs d\'optimisation ont été détectées');

            return 1;
        }

        $this->info(' [PHP Unit] Vérification des tests PHP');
        $bar->advance();
        $this->info(PHP_EOL);

        if ($this->runPHPUnit()) {
            $this->output->error('Des erreurs ont été rencontrées lors de la game');

            return 1;
        }

        $this->info(PHP_EOL);
        $bar->advance();
        $this->info(PHP_EOL);

        $this->output->success('Code parfait √');
    }

    /**
     * Lance php -l pour vérifier la syntaxe
     *
     * @return integer
     */
    private function runPHPSyntax()
    {
        $files = $this->files;

        if (count($files) === 0) {
            $command = "find {".implode($this->dirs, ',')."} -type f -name '*.php'";

            $process = new Process($command);

            $process->run(function ($type, $line) use (&$files) {
                $files = explode("\n", $line);
            });
        }

        foreach ($files as $file) {
            $process = new Process("php -l ".$file);
            $lines = [];

            $process->run(function ($type, $line) use (&$lines) {
                $lines[] = $line;
            });

            if ($process->getExitCode()) {
                $this->output->write($lines);

                return 1;
            }
        }

        return 0;
    }

    /**
     * Lance le PHP Code Sniffer pour vérifier le style PHP
     *
     * @return integer
     */
    private function runPHPCS()
    {
        return $this->process(
            "./vendor/bin/phpcs ".implode($this->files, ' ')
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
            "./vendor/bin/phpcbf ".implode($this->files, ' ')
        );
    }

    /**
     * Lance le PHP Code Beautifer and Fixer pour corriger à la volée les problèmes de styles
     *
     * @return integer
     */
    private function runPHPMD()
    {
        $files = $this->files;

        if (count($files) === 0) {
            $files = $dir;
        }

        $files = implode($files, ',');

        return $this->process(
            "./vendor/bin/phpmd ".$files.' text phpmd.xml'
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
     * @param string $command Commande à lancer.
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
