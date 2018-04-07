<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title', validation_max('title'));
			$table->text('description', validation_max('description'))->nullable();
			$table->string('image', validation_max('url'))->nullable();
			$table->timestamp('from')->useCurrent();
			$table->timestamp('to')->useCurrent();
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');
			$table->string('place')->nullable();        // TODO taille max ?

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
		Schema::dropIfExists('events');
	}
}
