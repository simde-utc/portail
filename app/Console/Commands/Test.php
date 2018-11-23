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
    protected $signature = 'quick:test {file?*} {--special}';

    /**
     * @var string
     */
    protected $description = 'Teste le code avant de pouvoir push le code';

    /**
     * Tous les dossiers à vérifier.
     *
     * @var array
     */
    protected $dirs = [
        'app', 'bootstrap', 'config', 'database', 'resources/lang', 'routes', 'tests',
    ];

    /**
     * Tous les dossiers à vérifier d'une manière particulière.
     *
     * @var array
     */
    protected $specialFiles = [
        'config', 'resources/lang', 'routes', 'database'
    ];

    /**
     * Tous les fichiers à tester.
     *
     * @var array
     */
    protected $files;

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

        if ($this->runPHPSyntax()) {
            $this->output->error('Des erreurs de syntaxe ont été détectées');

            return 1;
        }

        $this->info(PHP_EOL);
        $bar->advance();
        $this->info(PHP_EOL);
        $this->info(PHP_EOL);
        $this->info(' [PHP CS] Vérification du linting PHP');

        if ($this->runPHPCS()) {
            $this->output->error('Des erreurs ont été rencontrées lors de la vérification du linting');

            $value = $this->choice('Tenter de fixer les erreurs ?', ['Oui', 'Non'], 'Non');

            if ($value === 'Oui') {
                $this->runPHPCBF();

                if ($this->runPHPCS()) {
                    $this->output->error('Des erreurs n\'ont pas pu être corrigées lors de la vérification du linting');

                    return 2;
                }
            } else {
                return 2;
            }
        }

        $this->info(PHP_EOL);
        $bar->advance();
        $this->info(PHP_EOL);
        $this->info(PHP_EOL);
        $this->info(' [PHP STAN] Vérification du code PHP');

        if ($this->runPHPStan()) {
            $this->output->error('Des erreurs de code ont été détectées');

            return 3;
        }

        $bar->advance();
        $this->info(PHP_EOL);
        $this->info(PHP_EOL);
        $this->info(' [PHP MD] Vérification des optimisations PHP');

        if ($this->runPHPMD()) {
            $this->output->error('Des erreurs d\'optimisation ont été détectées');

            return 4;
        }

        $this->info(PHP_EOL);
        $bar->advance();
        $this->info(PHP_EOL);
        $this->info(PHP_EOL);
        $this->info(' [PHP Unit] Vérification des tests PHP');

        if ($this->runPHPUnit()) {
            $this->output->error('Des erreurs ont été rencontrées lors de la game');

            return 5;
        }

        $this->info(PHP_EOL);
        $bar->advance();
        $this->info(PHP_EOL);
        $this->info(PHP_EOL);

        $this->output->success('Code parfait √');
    }

    /**
     * Lance php -l pour vérifier la syntaxe.
     *
     * @return integer
     */
    private function runPHPSyntax()
    {
        $files = $this->files;
        $failed = false;

        if (count($files) === 0) {
            $files = [];

            $bar = $this->output->createProgressBar(count($this->dirs));

            foreach ($this->dirs as $dir) {
                $command = "find ".$dir." -iname '*.php' -exec php -l '{}' \; | grep '^No syntax errors' -v";

                $process = new Process($command);

                $process->run(function ($type, $line) use (&$failed) {
                    if ($line !== '') {
                        $this->output->write($line);
                        $failed = true;
                    }
                });

                if ($failed) {
                    return 1;
                }

                $bar->advance();
            }
        } else {
            $bar = $this->output->createProgressBar(count($files));

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

                $bar->advance();
            }
        }

        return 0;
    }

    /**
     * Lance le PHP Code Sniffer pour vérifier le style PHP.
     *
     * @return integer
     */
    private function runPHPCS()
    {
        $excludedRules = [
            'Generic.Files.LineLength',
            'Squiz.Commenting.InlineComment'
        ];

        // Les fichiers sont du type "spécial" (possèdent des règles en moins)
        if ($this->option('special')) {
            $specialFiles = $this->files;
        }
        else {
            $files = [];
            $specialFiles = [];

            foreach ($this->files as $file) {
                if (array_search($file, $this->specialFiles) === false) {
                    $files[] = $file;
                }
                else {
                    $specialFiles[] = $file;
                }
            }

            if ($code = $this->process("./vendor/bin/phpcs ".implode($files, ' '))) {
                return $code;
            }
        }

        return $this->process(
            "./vendor/bin/phpcs ".implode($specialFiles, ' ')." --exclude=".implode($excludedRules, ',')
        );
    }

    /**
     * Lance le PHP Code Beautifer and Fixer pour corriger à la volée les problèmes de styles.
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
     * Lance le PHP Code Beautifer and Fixer pour corriger à la volée les problèmes de styles.
     *
     * @return integer
     */
    private function runPHPStan()
    {
        $files = $this->files;

        if (count($files) === 0) {
            $files = $this->dirs;
        }

        $files = implode($files, ',');

        return $this->process(
            "php artisan code:analyse -p ".$files
        );
    }

    /**
     * Lance le PHP Code Beautifer and Fixer pour corriger à la volée les problèmes de styles.
     *
     * @return integer
     */
    private function runPHPMD()
    {
        $files = $this->files;

        if (count($files) === 0) {
            $files = $this->dirs;
        }

        $files = implode($files, ',');

        return $this->process(
            "./vendor/bin/phpmd ".$files.' text phpmd.xml'
        );
    }

    /**
     * Lance le PHP Unit pour tester que le code n'a pas cassé.
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
     * Lance une commande bash.
     *
     * @param string $command Commande à lancer.
     * @return integer
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
