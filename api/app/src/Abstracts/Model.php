<?php namespace Abstracts;

use k\Reposed;
use k\Reposed\FluentRelation;

class Model extends Reposed\SnakeModel {

	protected $visible = null;

	protected static $unguarded = true;

	public static $snakeAttributes = true;

	protected $serializedDateFormat = 'd-M-Y g:ia';

	/**
	 * Overriding static::repo to use 5.5 ::class scalar
	 */
	public static function repo()
	{
		return Reposed\Facade::make(static::class);
	}

	////////////////////////////////////////////////////
	
	public function getSerializedDateFormat()
	{
		return $this->serializedDateFormat;
	}

	////////////////////////////////////////////////////

	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}

	public function newRepository()
	{
		return new Repository($this);
	}

	public function relationIsToMany($relation)
	{
		$relation = camel_case($relation);
		$relation = $this->$relation();

		$class = get_class($relation);

		return $relation instanceof FluentRelation ? $relation->getIsToMany() : ends_with($class, 'ToMany');
	}

	public function relationIsToOne($relation)
	{
		return ! $this->relationIsToMany();
	}

	/////////////////////////////////////////////////

	public function newValidator(array $data = array())
	{
		if($this->exists) return $this->newUpdationValidator($data);
		else              return $this->newCreationValidator($data);
	}

	public function newPersistor(array $data = array())
	{
		if($this->exists) return $this->newUpdationPersistor($data);
		else              return $this->newCreationPersistor($data);
	}

	/**
	 * created at and updated at serializers
	 */
	public function serializeCreatedAt()
	{
		return $this->createdAt->format($this->getSerializedDateFormat());
	}

	public function serializeUpdatedAt()
	{
		return $this->updatedAt->format($this->getSerializedDateFormat());
	}

}