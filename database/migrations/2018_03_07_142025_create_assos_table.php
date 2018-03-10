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
            $table->string('name');
            $table->string('login');
            $table->text('description');
            $table->integer('type_asso_id')->unsigned();
            $table->foreign('type_asso_id')->references('id')->on('assos_types');
            $table->integer('parent_id')->unsigned()->nullable();
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
