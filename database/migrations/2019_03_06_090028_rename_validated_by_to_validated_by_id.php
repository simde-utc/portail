<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameValidatedByToValidatedById extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assos_members', function(Blueprint $table) {
            $table->renameColumn('validated_by', 'validated_by_id');
        });

        Schema::table('assos_permissions', function(Blueprint $table) {
            $table->renameColumn('validated_by', 'validated_by_id');
        });

        Schema::table('users_roles', function(Blueprint $table) {
            $table->renameColumn('validated_by', 'validated_by_id');
        });

        Schema::table('users_permissions', function(Blueprint $table) {
            $table->renameColumn('validated_by', 'validated_by_id');
        });

        Schema::table('groups_members', function(Blueprint $table) {
            $table->renameColumn('validated_by', 'validated_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assos_members', function(Blueprint $table) {
            $table->renameColumn('validated_by_id', 'validated_by');
        });

        Schema::table('assos_permissions', function(Blueprint $table) {
            $table->renameColumn('validated_by_id', 'validated_by');
        });

        Schema::table('users_roles', function(Blueprint $table) {
            $table->renameColumn('validated_by_id', 'validated_by');
        });

        Schema::table('users_permissions', function(Blueprint $table) {
            $table->renameColumn('validated_by_id', 'validated_by');
        });

        Schema::table('groups_members', function(Blueprint $table) {
            $table->renameColumn('validated_by_id', 'validated_by');
        });
    }
}
