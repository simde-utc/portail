<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesActionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articles_actions', function (Blueprint $table) {
			$table->integer('article_id')->unsigned();
			$table->foreign('article_id')->references('id')->on('articles');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->string('key');
			$table->string('value')->nullable();
			$table->enum('type', [
				'STRING', 'INTEGER', 'DOUBLE', 'BOOLEAN', 'ARRAY', 'DATETIME', 'NULL',
			])->default('STRING');
			$table->integer('visibility_id')->unsigned();
			$table->foreign('visibility_id')->references('id')->on('visibilities');

			$table->timestamps();
			$table->primary(['article_id', 'user_id', 'key']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('articles_actions');
	}
}
