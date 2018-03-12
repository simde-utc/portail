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
			$table->string('description', 128)->nullable();
			$table->string('image', 128)->nullable();
			$table->timestamp('date_from');
			$table->timestamp('date_to');
            $table->timestamps();
			$table->string('visibility', 7);
			$table->string('place', 128)->nullable();
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
