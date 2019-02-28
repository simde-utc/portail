<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_permissions', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('user_id');
            $table->uuid('permission_id');
            $table->uuid('semester_id');
            $table->uuid('validated_by')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->foreign('validated_by')->references('id')->on('users');

            $table->primary(['user_id', 'permission_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_permissions');
    }
}
