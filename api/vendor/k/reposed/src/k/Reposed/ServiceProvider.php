<?php namespace k\Reposed;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider {

	public function register()
	{
		$this->app['reposed.factory'] = $this->app->share(function ($app) {
			return new RepositoryFactory($app['cache'] ? $app['cache']->driver() : null);
		});
	}

}