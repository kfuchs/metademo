<?php

use Controller\Api;

Route::group(['before' => 'auth'], function () {
	Route::get('users', Api\Users::action('index'));
	Route::get('users/{user}', Api\Users::action('show'));

	Route::get('widgets', Api\Widgets::action('index'));
	Route::get('widgets/{widget}', Api\Widgets::action('show'));
	Route::post('widgets', Api\Widgets::action('create'));
	Route::put('widgets/{widget}', Api\Widgets::action('update'));
	Route::post('widgets/{widget}', Api\Widgets::action('update'));
	Route::delete('widgets/{widget}', Api\Widgets::action('destroy'));

	Route::get('manufacturers', Api\Manufacturers::action('index'));
	Route::get('manufacturers/{manufacturers}', Api\Manufacturers::action('show'));
	Route::post('manufacturers', Api\Manufacturers::action('create'));
	Route::put('manufacturers/{manufacturer}', Api\Manufacturers::action('update'));
	Route::delete('manufacturers/{manufactuerer}', Api\Manufacturers::action('destroy'));

	Route::get('widget-tags', Api\Widget\Tags::action('index'));
	Route::get('widget-tags/{tag}', Api\Widget\Tags::action('show'));
	Route::post('widget-tags', Api\Widget\Tags::action('create'));
	Route::put('widget-tags/{tag}', Api\Widget\Tags::action('update'));
	Route::delete('widget-tags/{tag}', Api\Widget\Tags::action('destroy'));

	Route::post('widgets/{widget}/widget-tags/{tag}', Api\Widgets::action('attachTag'));
	Route::delete('widgets/{widget}/widget-tags/{tag}', Api\Widgets::action('detachTag'));

	Route::get('user-roles', Api\User\Roles::action('index'));
	Route::get('user-roles/{role}', Api\User\Roles::action('show'));
	Route::post('user-roles', Api\User\Roles::action('create'));
	Route::put('user-roles/{role}', Api\User\Roles::action('update'));
	Route::delete('user-roles/{role}', Api\User\Roles::action('destroy'));

	Route::post('users/{user}/user-roles/{role}', Api\Users::action('attachRole'));
	Route::delete('users/{user}/user-roles/{role}', Api\Users::action('detachRole'));

});