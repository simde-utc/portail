<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesParentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles_parents', function (Blueprint $table) {
			$table->uuid('role_id');
			$table->uuid('parent_id');

			$table->timestamps();

			$table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
			$table->foreign('parent_id')->references('id')->on('roles');

			$table->primary(['parent_id', 'role_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles_parents');
	}
}
