<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quick:test {file*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste le code avant de pouvoir push le code';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        // Run Code Beautifier and Fixer...
        if ($this->runPHPCS()) {
            $this->output->error('Des erreurs ont été rencontrées lors de la vérification du linting');

            $value = $this->choice('Tenter de fixer les erreurs ?', ['Oui', 'Non'], 1);

            if ($value === 'Oui') {
                $this->runPHPCBF();

                if ($this->runPHPCS()) {
                    $this->output->error('Des erreurs n\'ont pas pu être corrigées lors de la vérification du linting');

                    exit(1);
                }
            }
            else {
                exit(1);
            }
        }

        $this->runPHPUnit();

        $this->output->success('Code parfait √');
    }

    /**
     * Run Code Sniffer to detect PSR2 code standard.
     */
    private function runPHPCS()
    {
        return $this->process(
            "./vendor/bin/phpcs"
        );
    }

    /**
     * Run Code Beautifier and Fixer.
     */
    private function runPHPCBF()
    {
        $this->process(
            "./vendor/bin/phpcbf"
        );
    }

    /**
     * Run PHP Unit test.
     */
    private function runPHPUnit()
    {
        return $this->process(
            "./vendor/bin/phpunit"
        );
    }

    /**
     * @param $command
     * @return Process
     */
    private function process($command)
    {
        $process = new Process($command);

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        return $process->getExitCode();
    }
}
