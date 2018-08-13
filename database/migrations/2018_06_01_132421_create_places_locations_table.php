<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacesLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 128);
            $table->uuid('place_id');
            $table->point('position')->nullable();

  			$table->timestamps();
            $table->softDeletes();
            
            $table->foreign('place_id')->references('id')->on('places');

            $table->unique(['name', 'place_id']);
  		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places_locations');
    }
}
