<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function (Blueprint $table) {
			$table->integer('room_id')->unsigned();
			$table->foreign('room_id')->references('id')->on('rooms');
			$table->integer('asso_id')->unsigned();
			$table->foreign('asso_id')->references('id')->on('assos');
			$table->timestamp('from')->nullable();
			$table->timestamp('to')->nullable();
			$table->integer('id_user')->unsigned();
			$table->foreign('id_user')->references('id')->on('users');
			$table->string('description', 250)->nullable();
			$table->integer('reservation_type_id')->unsigned();
			$table->foreign('reservation_type_id')->references('id')->on('reservation_types');
			$table->primary(['room_id', 'asso_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reservations');
	}
}
