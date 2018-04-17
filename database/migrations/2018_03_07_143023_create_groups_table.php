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
			$table->integer('user_id')->unsigned()->nullable(); // Correspond ici au créateur, pas à l'admin du groupe (qui peut changer)
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('name', validation_max('name'));
			$table->string('icon')->nullable();
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');

			$table->timestamps();
			$table->softDeletes();
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
