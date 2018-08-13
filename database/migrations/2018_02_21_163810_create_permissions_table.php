<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('type', 64)->unique();
			$table->string('name', 128);
			$table->string('description');
			$table->string('limited_at')->nullable();
			$table->string('only_for', 64)->default('users');

			$table->timestamps();
			
			$table->unique(['type', 'only_for']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permissions');
	}
}
