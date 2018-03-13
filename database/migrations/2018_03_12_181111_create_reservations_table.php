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
            $table->foreign('asso')->references('numero')->on('assos')

            $table->timestamp('date_from')->nullable();
            $table->timestamp('date_to')->nullable();
            $table->string('id_user');
            $table->foreign('id_user')->references('email')->on('users');
            $table->string('comment', 250)->nullable();
            $table->string('type');
            $table->foreign('type')->references('id')->on('reservation_types');
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
