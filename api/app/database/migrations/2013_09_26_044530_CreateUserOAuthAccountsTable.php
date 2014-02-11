<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserOAuthAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_oauth_accounts', function ($t) {
			$t->increments('id');
			$t->integer('user_id')->index();
			$t->string('provider');
			$t->string('token');
			$t->string('account_id');
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
		Schema::drop('user_oauth_accounts');
	}

}