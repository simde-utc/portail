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
            $table->increments('id');
            $table->string('name', 128);
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('places_locations');
            $table->timestamp('begin_at')->useCurrent();
            $table->timestamp('end_at')->useCurrent();
            $table->boolean('full_day')->default(false); // Les horaires seront ignorés si vrai
            $table->morphs('created_by');

  			$table->timestamps();
            $table->unique(['name', 'location_id', 'begin_at', 'end_at', 'full_day', 'created_by_id', 'created_by_type'], 'events_n_l_b_e_f_c');
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
