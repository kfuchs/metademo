<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function ($t) {
			$t->increments('id');
			$t->string('username');
			$t->string('email')->unique();
			$t->string('password');
			$t->string('profile_photo')->nullable();
			$t->string('first_name')->nullable();
			$t->string('last_name')->nullable();
			$t->boolean('is_active')->nullable()->index();
			$t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}