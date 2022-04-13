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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
			$table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('category');
            $table->text('productDetails');
            $table->float('price');
			$table->integer('quantity')->default('0');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('likes')->default('0');
            $table->integer('weight')->default('0');
            $table->integer('length')->default('0');
            $table->integer('width')->default('0');
            $table->integer('height')->default('0');
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
        Schema::dropIfExists('product');
    }
};
