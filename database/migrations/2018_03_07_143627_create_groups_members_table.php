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
			$table->uuid('group_id');
			$table->uuid('user_id');
			$table->uuid('role_id')->nullable();
			$table->uuid('semester_id'); // On permet ici que le semestre soit égal à 0 pour ne convenir à aucun semestre
			$table->uuid('validated_by')->nullable();

			$table->timestamps();

			$table->foreign('group_id')->references('id')->on('groups');
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('role_id')->references('id')->on('roles');
			$table->foreign('validated_by')->references('id')->on('users');

			$table->primary(['group_id', 'user_id', 'semester_id']);
		});

		Schema::create('groups_permissions', function (Blueprint $table) {
			$table->uuid('group_id');
			$table->uuid('user_id');
			$table->uuid('permission_id')->nullable();
			$table->uuid('semester_id'); // On permet ici que le semestre soit égal à 0 pour ne convenir à aucun semestre
			$table->uuid('validated_by')->nullable();

			$table->timestamps();

			$table->primary(['group_id', 'user_id', 'permission_id', 'semester_id'], 'group_permissions_user_semester'); // Unique pour permettre semester_id d'être nulle
		});

		// En fait Laravel fait dans l'ordre, du coup le primary plante..
		// https://github.com/laravel/framework/issues/25190
		Schema::table('groups_permissions', function (Blueprint $table) {
			$table->foreign('group_id')->references('id')->on('groups');
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('permission_id')->references('id')->on('permissions');
			$table->foreign('validated_by')->references('id')->on('users');
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
