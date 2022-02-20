<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration {

	public function up()
	{
		Schema::create('category', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name', 200)->unique();
			$table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
		});
	}

	public function down()
	{
		Schema::drop('category');
	}
}