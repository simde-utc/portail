<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Update extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quick:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update the whole application';

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
		$bar = $this->output->createProgressBar(7);
		$bar->advance();

		// Clear all
		$this->info(' [Quick Update] Cleaning');
		$this->call('quick:clear');
		$bar->advance();

		// Update
		$this->info(' [Quick Update] Updating Composer dependencies');
		shell_exec('composer update');
		$bar->advance();

		$this->info(' [Quick Update] Updating Node dependencies');
		shell_exec('npm update');
		shell_exec('npm run dev');
		$bar->advance();

		// Clear all
		$this->info(' [Quick Update] Cleaning');
		$this->call('quick:clear');
		$bar->advance();

		// Migrating
		$this->info(' [Quick Update] Migrating');
		if ($this->confirm('Erase the database ?'))
			$this->call('migrate:fresh');
		else
			$this->call('migrate');

		if ($this->confirm('Seed the database ?'))
			$this->call("db:seed");
		$bar->advance();

		// Optimization
		$this->info(' [Quick Update] Optimizing');
		$this->call('quick:optimize');
		$bar->advance();

		// Finish
		$bar->finish();
		$this->info(' [Quick Update] Installation finished !');
	}
}
