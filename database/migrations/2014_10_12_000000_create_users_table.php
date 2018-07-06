<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('email', validation_max('email'))->unique();
			$table->string('firstname', validation_max('name'))->nullable();
			$table->string('lastname', validation_max('name'))->nullable();
			$table->boolean('is_active')->default(1);
			$table->rememberToken();
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
		Schema::dropIfExists('users');
	}
}
