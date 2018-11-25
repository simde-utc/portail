<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 128);
            $table->string('description')->nullable();
            $table->string('color', 9)->nullable();
            $table->uuid('visibility_id');
            $table->uuid('created_by_id')->nullable();
            $table->string('created_by_type')->nullable();
            $table->uuid('owned_by_id')->nullable();
            $table->string('owned_by_type')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('visibility_id')->references('id')->on('visibilities');

            $table->unique(['name', 'owned_by_id', 'owned_by_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendars');
    }
}
