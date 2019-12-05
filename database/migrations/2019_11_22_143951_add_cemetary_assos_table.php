<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCemetaryAssosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assos', function (Blueprint $table) {
            $table->timestamp('in_cemetery_at')->nullable();
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
            $table->dropColumn(['in_cemetery_at']);
        });
    }
}
