<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('photo_file');
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->unsignedBigInteger('updater_user_id')->nullable();
            $table->timestamps();

            $table->foreign('creator_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('SET NULL');

            $table->foreign('updater_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
