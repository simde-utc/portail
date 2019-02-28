<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access', function (Blueprint $table) {
			$table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->string('type', validation_max('name'))->unique();
            $table->string('name', validation_max('name'))->unique();
            $table->string('description')->nullable();
            $table->integer('utc_access')->nullable();

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
        Schema::dropIfExists('access');
    }
}
