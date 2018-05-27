<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPreferencesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_preferences', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('key');
			$table->string('value')->nullable();
			$table->enum('type', [
				'STRING', 'INTEGER', 'DOUBLE', 'BOOLEAN', 'ARRAY', 'DATETIME', 'NULL',
			])->default('STRING');
			$table->string('only_for')->default('global');

			$table->timestamps();
			$table->primary(['user_id', 'key', 'only_for']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users_preferences');
	}
}
