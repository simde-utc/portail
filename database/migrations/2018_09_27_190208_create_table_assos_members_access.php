<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAssosMembersAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assos_members_access', function (Blueprint $table) {
			$table->uuid('asso_id');
            $table->uuid('user_id');
            $table->uuid('confirmed_by_id')->nullable();
			$table->uuid('access_id');
			$table->uuid('semester_id');
            $table->uuid('validated_by_id')->nullable();
            $table->boolean('validated')->default(false);
            $table->string('description')->nullable();
            $table->string('comment')->nullable();

			$table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
			$table->foreign('asso_id')->references('id')->on('assos');
			$table->foreign('confirmed_by_id')->references('id')->on('users');
			$table->foreign('access_id')->references('id')->on('access');
            $table->foreign('semester_id')->references('id')->on('semesters');
			$table->foreign('validated_by_id')->references('id')->on('users');

            // On ne bloque pas avec le statut pour permettre multiple refus et multiple demande
			$table->unique(['asso_id', 'user_id', 'semester_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assos_members_access');
    }
}
