<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsAssosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contacts_assos', function (Blueprint $table) {
			$table->increments('id');
			$table->string('type', validation_max('string'));       // TODO taille
			$table->string('body');                                 // TODO
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');
			$table->integer('asso_id')->unsigned();
			$table->foreign('asso_id')->references('id')->on('assos');
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
		Schema::dropIfExists('contacts_assos');
	}
}
