<?php namespace k\Reposed;

use Illuminate\Cache\Repository as Cache;

class RepositoryFactory {

	protected $cache;

	public function __construct(Cache $cache = null)
	{
		$this->cache = $cache;
	}

	public function make($model)
	{
		if(! $model instanceof Model)
		{
			$model = new $model;
		}

		$repository = $model->newRepository();

		if($this->cache)
		{
			$repository->setCache($this->cache);
		}

		return $repository;
	}

	public function of($model)
	{
		return $this->make($model);
	}

}