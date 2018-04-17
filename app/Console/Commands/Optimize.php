<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Optimize extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quick:optimize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Optimize the application by caching resources';

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
		$this->call('view:cache');
		$this->call('route:cache');
		$this->call('config:cache');
	}
}
