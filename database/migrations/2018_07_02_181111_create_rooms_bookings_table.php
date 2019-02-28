<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms_bookings', function (Blueprint $table) {
			$table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->uuid('room_id');
            $table->uuid('type_id')->nullable();
            // Aucun type si on bloque les réservations via l'admin par ex
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
            $table->foreign('type_id')->references('id')->on('rooms_bookings_types');
            $table->foreign('event_id')->references('id')->on('events');

            $table->unique(['room_id', 'event_id', 'owned_by_id', 'owned_by_type'], 'booking_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms_bookings');
    }
}
