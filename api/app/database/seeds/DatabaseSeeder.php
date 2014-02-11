<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		DB::connection()->disableQueryLog();

		DB::transaction(function () {
		});

		DB::connection()->enableQueryLog();
	}

}
