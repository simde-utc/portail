<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAssosAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assos_access', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asso_id');
            $table->uuid('access_id');
            $table->uuid('semester_id');
            $table->uuid('member_id');
            $table->uuid('confirmed_by_id')->nullable();
            $table->uuid('validated_by_id')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('description');
            $table->string('comment')->nullable();
            $table->boolean('validated')->default(false);

            $table->timestamps();

            $table->foreign('asso_id')->references('id')->on('assos');
            $table->foreign('access_id')->references('id')->on('access');
            $table->foreign('member_id')->references('id')->on('users');
            $table->foreign('confirmed_by_id')->references('id')->on('users');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->foreign('validated_by_id')->references('id')->on('users');

            // On ne bloque pas avec le statut pour permettre multiple refus et multiple demande
            $table->unique(['asso_id', 'member_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assos_access');
    }
}
