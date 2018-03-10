<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssosTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assos_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description'); // Ce champ permettra par exemple d'avoir plusieurs types de 1901 avec des comportements différents mais une unique description : "Association loi 1901".
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
        Schema::dropIfExists('assos_types');
    }
}
