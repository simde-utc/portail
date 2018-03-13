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
            
            $table->integer('salle')->unsigned();
            $table->foreign('salle')->references('id')->on('rooms');

            $table->integer('asso')->unsigned();
            $table->foreign('asso')->references('id')->on('assos');

            $table->timestamp('date_from')->nullable();
            $table->timestamp('date_to')->nullable();

            $table->integer('id_user')->unsigned();
            $table->foreign('id_user')->references('id')->on('users');
            
            $table->string('comment', 250)->nullable();
            
            $table->integer('type')->unsigned();
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
