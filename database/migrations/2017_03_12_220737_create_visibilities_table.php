<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visibilities', function (Blueprint $table) {
			$table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->uuid('id')->primary();
            $table->string('type', validation_max('type'))->unique();
            $table->string('name', validation_max('name'))->unique();
            $table->uuid('parent_id')->nullable();

            $table->timestamps();
        });

        // Obligé d'ajouter la contrainte après création... #LaravelBug
        // https://github.com/laravel/framework/issues/25190
        Schema::table('visibilities', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('visibilities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visibilities');
    }
}
