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
            $table->string('title', 128);
            $table->text('description')->nullable();
            $table->string('image', 128)->nullable();
            $table->timestamp('date_from')->useCurrent();
            $table->timestamp('date_to')->useCurrent();
            $table->integer('visibility_id')->unsigned();

            $table->foreign('visibility_id')->references('id')->on('visibilities');
            $table->string('place', 128)->nullable();
            
            $table->timestamps();
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
