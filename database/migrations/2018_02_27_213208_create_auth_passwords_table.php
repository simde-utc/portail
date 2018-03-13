<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthPasswordsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('auth_passwords', function (Blueprint $table) {
			$table->integer('user_id')->unsigned()->primary();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('password', 512);
			$table->timestamps();
			$table->timestamp('last_login_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('auth_passwords');
	}
}
