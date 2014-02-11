<?php namespace k;

function repo($model)
{
	static $factory;

	if(is_null($factory))
	{
		$factory = new Reposed\RepositoryFactory();
	}

	return $factory->repo($model);
}