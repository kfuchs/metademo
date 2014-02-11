<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_roles', function ($t) {
			$t->increments('id');
			$t->string('name');
			$t->timestamps();
		});

		Schema::create('user_user_role', function ($t) {
			$t->increments('id');
			$t->integer('user_id')->index();
			$t->integer('role_id')->index();
			$t->timestamps();

			$t->unique(['user_id', 'role_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_roles');
		Schema::drop('user_user_role');
	}

}