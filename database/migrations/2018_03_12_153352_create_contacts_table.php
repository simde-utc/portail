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
			$table->uuid('id')->primary();
			$table->string('name', validation_max('name'));
			$table->string('value', validation_max('url'));
			$table->uuid('contact_type_id');
			$table->uuid('visibility_id');
			$table->nullableMorphs('owned_by');

			$table->timestamps();

			$table->foreign('contact_type_id')->references('id')->on('contacts_types');
			$table->foreign('visibility_id')->references('id')->on('visibilities');

			$table->unique(['name', 'contact_type_id', 'owned_by_type', 'owned_by_id']);
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
