<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->string('numero', 20);
            $table->string('name', 128);
            $table->string('pole', 128);
            
            /* LIGNE A RAJOUTER QUAND TABLE ASSO CREE

            ->references('login')->on('asso');
            
            */

            $table->primary('numero');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
