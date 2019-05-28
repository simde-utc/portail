<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs_categories', function(Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->string('name', validation_max('name'))->unique();
            $table->string('description', validation_max('description'));
            $table->string('lang', 2)->default('fr');
            $table->uuid('parent_id')->nullable();
            $table->uuid('visibility_id');

            $table->timestamps();
        });

        Schema::table('faqs_categories', function(Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('faqs_categories');
            $table->foreign('visibility_id')->references('id')->on('visibilities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs_categories');
    }
}
