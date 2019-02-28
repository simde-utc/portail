<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthPasswordsResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_passwords_resets', function (Blueprint $table) {
			$table->charset = 'utf8';
            $table->collation = 'utf8_bin';
            $table->string('email', validation_max('email'))->index();
            $table->string('token', 64);

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_passwords_resets');
    }
}
