<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
*/

App::before(function($req) {
	if($origin = $req->headers->get('origin') and ! in_array($origin , Config::get('origins')))
	{
		// we check for the origin belonging to a chrome extension too
		// because we need to
		
		if(! starts_with($origin, 'chrome'))
		{
			return Response::notAllowed();
		}
	}

	Log::info(PHP_EOL.PHP_EOL.$req.PHP_EOL.PHP_EOL);
});


App::after(function($req, $res) {
	Auth::processResponse($res);
	Log::info(PHP_EOL.PHP_EOL.$res.PHP_EOL.PHP_EOL);

	if($origin = $req->headers->get('origin') and in_array($origin , Config::get('origins')))
	{
		$res->headers->set('Access-Control-Allow-Origin', $origin);
	}
});


/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
*/

Route::filter('auth', function ($route, $req) {

	if(! Auth::check($req))
	{
		return Response::unauthorized();
	}

});

/*
|--------------------------------------------------------------------------
| Location logging Filter
|--------------------------------------------------------------------------
|
*/