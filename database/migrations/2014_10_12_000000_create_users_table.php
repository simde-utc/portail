<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('prenom');
            $table->string('nom');

            /*
            $table->string('domaine');       // etu, ...
            $table->string('branche');       // GI, ...
            $table->string('filiere');       // FDD, ...
            $table->integer('telephone', 10);       // 06...
            $table->string('semestre', 5);       // 06...
            */

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
