<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTypesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contacts_types', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', validation_max('string'));
			$table->string('pattern', validation_max('string'));
			$table->integer('max')->unsigned();
			$table->integer('visibility_id')->unsigned();						// By default
			$table->foreign('visibility_id')->references('id')->on('visibilities');
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
		Schema::dropIfExists('contacts_types');
	}
}
