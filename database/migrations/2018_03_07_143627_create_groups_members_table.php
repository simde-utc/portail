<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsMembersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups_members', function (Blueprint $table) {
			$table->integer('group_id')->unsigned();
			$table->foreign('group_id')->references('id')->on('groups');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('role_id')->unsigned()->nullable();
			$table->foreign('role_id')->references('id')->on('roles');
			$table->integer('semester_id')->unsigned()->nullable();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');
			$table->text('permission_ids')->nullable(); // Permissions custom ajouté à la personne

			$table->timestamps();
			$table->primary(['group_id', 'user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('groups_members');
	}
}
