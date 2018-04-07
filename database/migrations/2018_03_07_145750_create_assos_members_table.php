<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssosMembersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assos_members', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('asso_id')->unsigned();
			$table->foreign('asso_id')->references('id')->on('assos');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			// TODO Role à voir
			$table->integer('semester_id')->unsigned();
			$table->foreign('semester_id')->references('id')->on('semesters');
			$table->index(['asso_id', 'user_id', 'semester_id']);
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
		Schema::dropIfExists('assos_members');
	}
}
