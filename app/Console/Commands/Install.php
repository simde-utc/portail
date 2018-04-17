<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quick:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install/update the whole application';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$bar = $this->output->createProgressBar(8);
		$bar->advance();

		// Installation
		$this->info(' [Quick Install] Installing Composer dependencies');
		shell_exec('composer install');
		$bar->advance();

		$this->info(' [Quick Install] Installing Node dependencies');
		shell_exec('npm install');
		$bar->advance();

		// Clear all
		$this->info(' [Quick Install] Cleaning');
		$this->call('quick:clear');
		$bar->advance();

		// Configuration
		$this->info(' [Quick Install] Setting .env');
		$this->call('key:generate');
		$this->call('passport:keys');

		// Copy .env and fill it up ?
		// $env = $this->choice('What is the environment ?', ['development', 'testing', 'production'], 0);
		$bar->advance();

		// Migrating
		$this->info(' [Quick Install] Migrating');
		if ($this->confirm('Erase the database ?'))
			$this->call('migrate:fresh');
		else
			$this->call('migrate');
		if ($this->confirm('Seed the database ?'))
			$this->call("db:seed");
		$bar->advance();
		
		// API Generation
		$this->info(' [Quick Install] Generating api');
		$this->callSilent('api:generate', [
			'--routePrefix' => 'api/*',
			'--actAsUserId' => 1
		]);
		$this->info('API Doc and collection.json generated');
		$bar->advance();

		// Optimization
		$this->info(' [Quick Install] Optimizing');
		$this->call('quick:optimize');
		$bar->advance();

		// Finish
		$bar->finish();
		$this->info(' [Quick Install] Installation finished !');
	}
}
