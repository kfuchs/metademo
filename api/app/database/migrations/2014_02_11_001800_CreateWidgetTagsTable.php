<?php

use Illuminate\Database\Migrations\Migration;

class CreateWidgetTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('widget_tags', function ($t) {
			$t->increments('id');
			$t->string('name')->unique();
			$t->timestamps();
		});

		Schema::create('widget_widget_tag', function ($t) {
			$t->increments('id');
			$t->integer('widget_id')->index();
			$t->integer('tag_id')->index();
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
		Schema::drop('widget_tags');
		Schema::drop('widget_widget_tag');
	}

}