<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('asso_id')->nullable();
            $table->string('name');
            $table->string('secret', 100);
            $table->text('redirect');
            $table->text('scopes')->nullable(); // Scopes définis pour le client credential
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');

            $table->timestamps();
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('asso_id')->references('id')->on('assos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_clients');
    }
}
