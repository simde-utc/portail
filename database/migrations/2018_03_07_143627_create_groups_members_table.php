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
			$table->integer('semester_id')->unsigned();
			//$table->foreign('semester_id')->references('id')->on('semesters'); On permet ici que le semestre soit égal à 0 pour ne convenir à aucun semestre
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');

			$table->timestamps();
			$table->primary(['group_id', 'user_id', 'semester_id']);
		});

		Schema::create('groups_permissions', function (Blueprint $table) {
			$table->integer('group_id')->unsigned();
			$table->foreign('group_id')->references('id')->on('groups');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('permission_id')->unsigned();
			$table->foreign('permission_id')->references('id')->on('permissions');
			$table->integer('semester_id')->unsigned();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');

			$table->timestamps();
			$table->primary(['group_id', 'user_id', 'permission_id', 'semester_id'], 'group_permissions_user_semester'); // Unique pour permettre semester_id d'être nulle
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
		Schema::dropIfExists('groups_permissions');
	}
}
