<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
			$table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->string('login', validation_max('login'))->unique();
            $table->string('shortname', validation_max('login'))->unique();
            $table->string('name', validation_max('name'))->unique();
            $table->string('image', validation_max('url'))->nullable();
            $table->string('url', validation_max('url'))->nullable();
            $table->text('description', validation_max('description'));
            $table->uuid('visibility_id');

            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('services');
    }
}
