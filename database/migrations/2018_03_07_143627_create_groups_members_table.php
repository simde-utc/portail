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
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('group_id');
            $table->uuid('user_id');
            $table->uuid('role_id')->nullable();
            $table->uuid('semester_id');
            // We allow here the semester to be equal to 0 (Between two semesters for example).
            $table->uuid('validated_by')->nullable();

            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('validated_by')->references('id')->on('users');

            $table->primary(['group_id', 'user_id', 'semester_id']);
        });

        Schema::create('groups_permissions', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('group_id');
            $table->uuid('user_id');
            $table->uuid('permission_id')->nullable();
            $table->uuid('semester_id');
            // We allow here the semester to be null (Between two semesters for example).
            $table->uuid('validated_by')->nullable();

            $table->timestamps();

            $table->primary(['group_id', 'user_id', 'permission_id', 'semester_id'], 'group_permissions_user_semester');
            // Unique to allow semester_id to be null.
        });

        // Laravel does it in the order so the primary key set crashes.
        // https://github.com/laravel/framework/issues/25190
        Schema::table('groups_permissions', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
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
