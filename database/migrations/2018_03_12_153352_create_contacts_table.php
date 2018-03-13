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
			$table->increments('id');
			$table->string('type', validation_max('string'));       // TODO taille
			$table->string('body');                                 // TODO
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');
			$table->integer('contactable_id')->unsigned();
      $table->string('contactable_type');
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
		Schema::dropIfExists('contacts');
	}
}
