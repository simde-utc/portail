<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars_followers', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('user_id');
            $table->uuid('calendar_id');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('calendar_id')->references('id')->on('calendars');

            $table->primary(['user_id', 'calendar_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendars_followers');
    }
}
