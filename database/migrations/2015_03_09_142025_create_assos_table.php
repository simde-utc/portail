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
			$table->uuid('id')->primary();
			$table->uuid('type_asso_id');
			$table->uuid('parent_id')->nullable();
			$table->string('login', validation_max('login'))->unique();
			$table->string('shortname', validation_max('login'))->unique();
			$table->string('name', validation_max('name'))->unique();
			$table->string('image', validation_max('url'))->nullable();
			$table->text('description', validation_max('description'));

			$table->timestamps();
			$table->softDeletes();
		});

		// https://github.com/laravel/framework/issues/25190
		Schema::table('assos', function (Blueprint $table) {
			$table->foreign('type_asso_id')->references('id')->on('assos_types');
			$table->foreign('parent_id')->references('id')->on('assos');
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
