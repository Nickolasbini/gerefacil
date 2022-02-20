<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('messageText', 200);
            $table->bigInteger('sender')->unsigned()->nullable();
            $table->foreign('sender')->references('id')->on('users');
            $table->bigInteger('receiver')->unsigned()->nullable();
            $table->foreign('receiver')->references('id')->on('users');
            $table->bigInteger('document')->unsigned()->nullable();
            $table->foreign('document')->references('id')->on('document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message');
    }
};
