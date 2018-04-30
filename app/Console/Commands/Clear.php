<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Clear extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quick:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear all compiled and cached resources';

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
		$this->call('view:clear');
		$this->call('route:clear');
		$this->call('config:clear');
		$this->call('debugbar:clear');
		$this->call('auth:clear-resets');
		$this->call('cache:clear');
		$this->call('clear-compiled');
	}
}
