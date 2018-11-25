<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->uuid('reservation_type_id');
            $table->uuid('event_id');
            $table->string('description')->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->string('created_by_type')->nullable();
            $table->uuid('owned_by_id')->nullable();
            $table->string('owned_by_type')->nullable();
            $table->uuid('validated_by_id')->nullable();
            $table->string('validated_by_type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('reservation_type_id')->references('id')->on('rooms_reservations_types');
            $table->foreign('event_id')->references('id')->on('events');

            $table->unique(['room_id', 'event_id', 'owned_by_id', 'owned_by_type'], 'reservation_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms_reservations');
    }
}
