<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('body');
            $table->uuid('parent_id')->nullable();
            $table->uuid('user_id');
            $table->uuid('visibility_id');
            $table->uuid('commentable_id');
            $table->string('commentable_type');

            $table->timestamps();
            $table->timestamp('deleted_at');
        });

        // Obligé d'ajouter la contrainte après création... #LaravelBug
        // https://github.com/laravel/framework/issues/25190
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('comments');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('visibility_id')->references('id')->on('visibilities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
