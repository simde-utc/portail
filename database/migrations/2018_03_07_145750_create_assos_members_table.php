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
        Schema::create('assos_members', function (Blueprint $table) {
            $table->uuid('asso_id');
            $table->uuid('user_id');
            $table->uuid('role_id')->nullable();
            $table->uuid('semester_id');
            $table->uuid('validated_by')->nullable();

            $table->timestamps();

            $table->foreign('asso_id')->references('id')->on('assos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->foreign('validated_by')->references('id')->on('users');

            $table->primary(['asso_id', 'user_id', 'semester_id']);
        });

        Schema::create('assos_permissions', function (Blueprint $table) {
            $table->uuid('asso_id');
            $table->uuid('user_id');
            $table->uuid('permission_id')->nullable();
            $table->uuid('semester_id');
            $table->uuid('validated_by')->nullable();

            $table->timestamps();

            $table->primary(['asso_id', 'user_id', 'permission_id', 'semester_id'], 'assos_permissions_user_semester');
        });

        // En fait Laravel fait dans l'ordre, du coup le primary plante..
        // https://github.com/laravel/framework/issues/25190
        Schema::table('assos_permissions', function (Blueprint $table) {
            $table->foreign('asso_id')->references('id')->on('assos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('semester_id')->references('id')->on('semesters');
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
        Schema::dropIfExists('assos_members');
        Schema::dropIfExists('assos_permissions');
    }
}
