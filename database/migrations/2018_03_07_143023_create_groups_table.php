<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('name', validation_max('name'));
			$table->string('icon')->nullable();                     // TODO taille max ?
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');
			$table->boolean('is_active')->default(1);
			$table->timestamps();
			$table->unique(['user_id', 'name']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('groups');
	}
}
