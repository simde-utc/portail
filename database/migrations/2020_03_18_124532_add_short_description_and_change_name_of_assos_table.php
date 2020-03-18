<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortDescriptionAndChangeNameOfAssosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assos', function (Blueprint $table) {
            $table->string('name', validation_max('name'))->nullable()->change();
            $table->string('short_description', validation_max("short_description"));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assos', function (Blueprint $table) {
            $table->dropColumn('short_description');
            $table->string('name', validation_max('name'))->nullable(false)->change();
        });
    }
}
