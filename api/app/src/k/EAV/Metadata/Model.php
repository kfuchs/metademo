<?php namespace k\EAV\Metadata;

use k\Reposed;
use Str;

class Model extends Reposed\Model {

	protected $ownerForeignKey = 'owner_id';
	protected $ownerModel = 'undefined';

	protected $fieldColumn = 'field';
	protected $valueColumn = 'value';

	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}

	public function owner()
	{
		return $this->belongsTo($this->getOwnerModel(), $this->getOwnerId());
	}

	public function getTable()
	{
		return Str::singular($this->owner->getTable()).'_metadata';
	}

	public function getOwnerForeignKey()
	{
		return $this->ownerForeignKey;
	}

	public function getOwnerModel()
	{
		return $this->ownerModel;
	}

	public function getFieldColumn()
	{
		return $this->fieldColumn;
	}

	public function getValueColumn()
	{
		return $this->valueColumn;
	}

	// migration helper method, just a static method to help with migrations
	
	public static function stdMigrationClosure()
	{
		return function ($t) {
			$t->increments('id');
			$t->integer('owner_id');
			$t->string('field');
			$t->string('value');
			$t->timestamps();
		};
	}

}