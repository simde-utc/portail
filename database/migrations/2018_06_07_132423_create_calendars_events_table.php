<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars_events', function (Blueprint $table) {
			$table->integer('calendar_id')->unsigned();
			$table->foreign('calendar_id')->references('id')->on('calendars');
			$table->integer('event_id')->unsigned();
			$table->foreign('event_id')->references('id')->on('events');

  			$table->timestamps();
            $table->unique(['calendar_id', 'event_id']);
  		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendars_events');
    }
}
