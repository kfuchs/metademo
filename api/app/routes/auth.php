<?php

Route::post('auth/login', Controller\Auth::action('login'));
Route::post('auth/register', Controller\Auth::action('register'));

Route::post('auth/logout', ['before' => 'auth', 'uses' => Controller\Auth::action('logout')]);

Route::get('auth/account', ['before' => 'auth', 'uses' => Controller\Auth::action('showAccount')]);