<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->string('salle')->references('numero')->on('rooms');
            $table->string('asso');

            /* A rajouter après création table Asso

            ->references('numero')->on('Asso')

            */

            $table->timestamp('date_from')->nullable();
            $table->timestamp('date_to')->nullable();
            $table->string('id_user')->references('email')->on('users');
            $table->string('comment', 250)->nullable();
            $table->enum('type', ['reunion', 'logistique', 'autre']);
            $table->primary(['salle', 'asso']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
