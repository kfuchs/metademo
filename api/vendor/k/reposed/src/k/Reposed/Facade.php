<?php namespace k\Reposed;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

	protected static function getFacadeAccessor()
	{
		return 'reposed.factory';
	}
}