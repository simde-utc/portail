<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->string('name', validation_max('name'));
            $table->string('value', validation_max('url'));
            $table->uuid('type_id');
            $table->uuid('visibility_id');
            $table->uuid('owned_by_id')->nullable();
            $table->string('owned_by_type')->nullable();

            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('contacts_types');
            $table->foreign('visibility_id')->references('id')->on('visibilities');

            $table->unique(['name', 'type_id', 'owned_by_type', 'owned_by_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
