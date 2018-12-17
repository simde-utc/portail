<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id');
            $table->string('key');
            $table->string('value')->nullable();
            $table->enum('type', [
                'STRING', 'INTEGER', 'DOUBLE', 'BOOLEAN', 'ARRAY', 'DATETIME', 'NULL',
            ])->default('STRING');

            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events');

            $table->unique(['event_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_details');
    }
}
