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
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->uuid('asso_id');
            $table->uuid('user_id');
            $table->uuid('reservation_type_id');
            $table->string('description', 250)->nullable();

            $table->timestamp('from')->useCurrent();
            $table->timestamp('to')->useCurrent();

            $table->foreign('room_id')->references('id')->on('reservations_rooms');
            $table->foreign('asso_id')->references('id')->on('assos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reservation_type_id')->references('id')->on('reservations_types');

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
