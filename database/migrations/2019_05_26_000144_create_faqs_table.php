<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function(Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->string('question', validation_max('title'))->unique();
            $table->string('answer', validation_max('description'));
            $table->uuid('category_id');
            $table->uuid('visibility_id');

            $table->foreign('category_id')->references('id')->on('faqs_categories');
            $table->foreign('visibility_id')->references('id')->on('visibilities');

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
          Schema::dropIfExists('faqs');
    }
}
