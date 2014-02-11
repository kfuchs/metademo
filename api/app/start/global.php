<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|-----------------------------------------------------------------------------
| Record Peak memory usage of the app
|-----------------------------------------------------------------------------
*/

App::before(function () {
	$perfFile = __DIR__.'/../storage/logs/perf';
	File::put($perfFile, Carbon\Carbon::now()->format('d M, Y H:i:s') . PHP_EOL);
	App::instance('start_time', microtime(true));

});

App::after(function () {
	$perfFile = __DIR__.'/../storage/logs/perf';

	File::append($perfFile, memory_get_peak_usage() . PHP_EOL);
	File::append($perfFile, microtime(true) - App::make('start_time'));
});

/*
|--------------------------------------------------------------------------
| Set the User Repository to be used by the auth package
|--------------------------------------------------------------------------
*/

Auth::setUserRepository(Core\User::repo());

/*
|--------------------------------------------------------------------------
| Require A few app settings files
|--------------------------------------------------------------------------
|
*/

require app_path().'/filters.php';
require app_path().'/events.php';
require app_path().'/error-handlers.php';