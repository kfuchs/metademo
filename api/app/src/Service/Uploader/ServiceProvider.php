<?php namespace Service\Uploader;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Guzzle\Http\Client;
use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Illuminate\Support\MessageBag;

class ServiceProvider extends IlluminateServiceProvider {

	protected function registerHttpClient()
	{
		$this->app['uploader.http-client'] = $this->app->share(function ($app) {
			return new HttpClient(function ($url) { return new Client($url); });
		});
	}

	protected function registerFilesystemAdapter()
	{
		$this->app['uploader.fs.adapter'] = $this->app->share(function ($app) {
			return new Adapter($app['config']->get('uploader.root.local'));
		});
	}

	protected function registerFilesystem()
	{
		$this->app['uploader.fs'] = $this->app->share(function ($app) {
			return new Filesystem($app['uploader.fs.adapter']);
		});
	}

	protected function registerUrlGenerator()
	{
		$this->app['uploader.url-generator'] = $this->app->share(function ($app) {
			return new UrlGenerator($app['uploader.fs'], $app['config']->get('uploader.url-base'));
		});
	}

	protected function registerManager()
	{
		$this->app['uploader.manager'] = $this->app->share(function ($app) {
			return new Manager(
				$app['uploader.fs'],
				$app['uploader.http-client'],
				$app['uploader.url-generator'],
				$app['validator']
			);
		});
	}

	public function register()
	{
		$this->registerHttpClient();
		$this->registerFilesystemAdapter();
		$this->registerFilesystem();
		$this->registerUrlGenerator();
		$this->registerManager();
	}

}