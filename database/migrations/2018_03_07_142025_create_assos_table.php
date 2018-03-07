<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('login');
            $table->string('photo');
            $table->text('description');
            $table->string('type_asso_id');
            $table->foreign('type_asso_id')-references('id')->on('type_assos');
            $table->string('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('assos');
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
        Schema::dropIfExists('assos');
    }
}
