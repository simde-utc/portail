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
			$table->string('name', validation_max('name'));
			$table->string('value', validation_max('url'));
			$table->integer('contact_type_id')->unsigned();
			$table->foreign('contact_type_id')->references('id')->on('contacts_types');
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');
			$table->nullableMorphs('owned_by');
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
