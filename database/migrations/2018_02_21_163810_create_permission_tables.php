<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('type')->unique();
			$table->string('name');
			$table->string('description');
			$table->string('limited_at')->nullable();
			$table->boolean('is_system')->default(false);

			$table->timestamps();
		});

		Schema::create('roles', function (Blueprint $table) {
			$table->increments('id');
			$table->string('type')->unique();
			$table->string('name');
			$table->string('description');
			$table->string('limited_at')->nullable();
			$table->string('only_for');

			$table->timestamps();
			$table->unique(['type', 'only_for'], 'roles_type_only_for');
		});

		Schema::create('roles_parents', function (Blueprint $table) {
			$table->integer('role_id')->unsigned();
			$table->foreign('role_id')
				->references('id')
				->on('roles')
				->onDelete('cascade');

			$table->integer('parent_id')->unsigned();
			$table->foreign('parent_id')
				->references('id')
				->on('roles');

			$table->timestamps();
			$table->primary(['parent_id', 'role_id'], 'roles_id_parent');
		});

		Schema::create('roles_permissions', function (Blueprint $table) {
			$table->integer('role_id')->unsigned();
			$table->foreign('role_id')
				->references('id')
				->on('roles')
				->onDelete('cascade');

			$table->integer('permission_id')->unsigned();
			$table->foreign('permission_id')
				->references('id')
				->on('permissions')
				->onDelete('cascade');

			$table->timestamps();
			$table->unique(['role_id', 'permission_id'], 'roles_id_perm');
		});

		Schema::create('users_roles', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('role_id')->unsigned();
			$table->foreign('role_id')
				->references('id')
				->on('roles');

			$table->integer('semester_id')->unsigned();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');

			$table->timestamps();
			$table->primary(['user_id', 'role_id', 'semester_id'], 'roles_id_user_sem');
		});

		Schema::create('users_permissions', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->integer('permission_id')->unsigned();
			$table->foreign('permission_id')
				->references('id')
				->on('permissions');

			$table->integer('semester_id')->unsigned();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->integer('validated_by')->unsigned()->nullable();
			$table->foreign('validated_by')->references('id')->on('users');

			$table->timestamps();
			$table->primary(['user_id', 'permission_id', 'semester_id'], 'roles_user_perm_sem');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permissions');
		Schema::drop('roles');
		Schema::drop('roles_parents');
		Schema::drop('roles_permissions');
		Schema::drop('users_permissions');
		Schema::drop('users_roles');
	}
}
