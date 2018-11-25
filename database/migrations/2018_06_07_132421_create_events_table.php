<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 128);
            $table->uuid('location_id')->nullable();
            $table->timestamp('begin_at')->useCurrent();
            $table->timestamp('end_at')->useCurrent();
            $table->uuid('full_day')->default(false);
            // Les horaires seront ignorés si vrai
            $table->uuid('visibility_id');
            $table->uuid('created_by_id')->nullable();
            $table->string('created_by_type')->nullable();
            $table->uuid('owned_by_id')->nullable();
            $table->string('owned_by_type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('location_id')->references('id')->on('places_locations');
            $table->foreign('visibility_id')->references('id')->on('visibilities');

            $table->unique(['name', 'location_id', 'begin_at', 'end_at', 'full_day', 'owned_by_id', 'owned_by_type'], 'events_n_l_b_e_f_c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
