<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssosMembersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assos_roles', function (Blueprint $table) {
			$table->integer('asso_id')->unsigned();
			$table->foreign('asso_id')->references('id')->on('assos');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('role_id')->unsigned()->nullable();
			$table->foreign('role_id')->references('id')->on('roles');
			$table->integer('semester_id')->unsigned();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');

			$table->timestamps();
			$table->primary(['asso_id', 'user_id', 'semester_id']);
		});

		Schema::create('assos_permissions', function (Blueprint $table) {
			$table->integer('asso_id')->unsigned();
			$table->foreign('asso_id')->references('id')->on('assos');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('permission_id')->unsigned();
			$table->foreign('permission_id')->references('id')->on('permissions');
			$table->integer('semester_id')->unsigned();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');

			$table->timestamps();
			$table->primary(['asso_id', 'user_id', 'permission_id', 'semester_id'], 'assos_permissions_user_semester');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('assos_roles');
		Schema::dropIfExists('assos_permissions');
	}
}
