<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthCasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('auth_cas', function (Blueprint $table) {
			$table->integer('user_id')->unsigned()->primary();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('login', validation_max('login'))->unique();
			$table->string('email', validation_max('email'))->unique();

			/*
			$table->string('domaine');       // etu, ...
			$table->string('branche');       // GI, ...
			$table->string('filiere');       // FDD, ...
			$table->integer('telephone', 10);       // 06...
			$table->string('semestre', 5);       // 06...
			*/

			$table->boolean('is_active')->default(1);
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
		Schema::dropIfExists('auth_cas');
	}
}
