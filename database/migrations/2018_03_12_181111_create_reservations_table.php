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
            $table->increments('id');
            $table->integer('room_id')->unsigned();
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->integer('asso_id')->unsigned();
            $table->foreign('asso_id')->references('id')->on('assos');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('reservation_type_id')->unsigned();
            $table->foreign('reservation_type_id')->references('id')->on('reservations_types');
            $table->string('description', 250)->nullable();
            $table->timestamp('from')->nullable();
            $table->timestamp('to')->nullable();
            $table->index(['room_id', 'asso_id', 'user_id', 'from']);
            $table->index(['room_id', 'asso_id', 'user_id', 'to']);
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
