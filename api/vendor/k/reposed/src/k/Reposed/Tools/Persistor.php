<?php namespace k\Reposed\Tools;

use k\Reposed\Model;
use DateTime;
use Closure;
use Illuminate\Database\Eloquent\Relations;

class Persistor {

	protected $model;
	protected $fields = array();
	protected $relations = array();
	protected $fieldProcessors = array();
	protected $relationProcessors = array();
	protected $data = array();

	protected $defaultFieldProcessor;

	public function __construct(Model $model = null)
	{
		$this->model = $model;
		$this->defaultFieldProcessor = function ($model, $field, $value) {
			$model->$field = $value;
		};
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function setFields(array $fields)
	{
		$this->fields = $fields;
		return $this;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setRelations(array $relations)
	{
		$this->relations = $relations;
		return $this;
	}

	public function getRelations()
	{
		return $this->relations;
	}

	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}

	public function getData()
	{
		return $this->data;
	}

	public function processedData()
	{
		$processed = array();

		foreach($this->data as $k => $v)
		{
			if (in_array($k, $this->model->getDates()))
			{
				$date = new DateTime;
				$v = strtotime($v);
				$date->setTimestamp((int) $v);
				$v = $date;
			}

			$processed[$k] = $v;
		}

		return $processed;
	}


	public function addFieldProcessor($field, Closure $processor)
	{
		$this->fieldProcessors[$field] = $processor;
		return $this;
	}

	public function removeFieldProcessor($field)
	{
		if(isset($this->fieldProcessors[$field]))
		{
			unset($this->fieldProcessors[$field]);
		}

		return $this;
	}

	public function addRelationProcessor($relation, Closure $processor)
	{
		$this->relationProcessors[$relation] = $processor;
		return $this;
	}

	public function removeRelationProcessor($relation)
	{
		if(isset($this->relationProcessors[$relation]))
		{
			unset($this->relationProcessors[$relation]);
		}

		return $this;
	}

	public function stdRelationProcessor($relation)
	{
		$r = $this->model->$relation();

		if($r === null)
			throw new \InvalidArgumentException('Relation: '. get_class($this->model) .'::'. $relation);

		switch ($r) {
			case $r instanceof Relations\BelongsTo:     return $this->belongsToRelationProcessor();
			case $r instanceof Relations\BelongsToMany: return $this->belongsToManyRelationProcessor();
			case $r instanceof Relations\HasMany:       return $this->hasManyRelationProcessor();
			case $r instanceof Relations\HasOne:        return $this->hasOneRelationProcessor();
			case $r instanceof Relations\MorphMany:     return $this->morphManyRelationProcessor();
			case $r instanceof Relations\MorphOne:      return $this->morphOneRelationProcessor();
			
			default: throw new \InvalidArgumentException('Relation: '. get_class($this->model) . $relation);
		}
	}

	public function belongsToRelationProcessor()
	{
		return function ($model, $relation, $value) {
			$key = $model->$relation->getForeignKey();
			$model->setAttribute($key, $value);
		};
	}

	public function belongsToManyRelationProcessor()
	{
		return function ($model, $relation, $value) {
			$r = $model->$relation();

			$ids = isset($value['ids']) ? (array) $value['ids'] : (array) $value;
			$operation = isset($value['operation']) ? $value['operation'] : 'sync';
			
			switch ($operation) {
				case 'sync'  :
					$model->$relation()->sync($ids);
				break;
				
				case 'merge' :
					$piv = $model->$relation()->getTable();
					$other = $model->$relation()->getOtherKey();

					foreach($ids as $id)
					{
						if(! $model->$relation()->where($piv.'.'.$other, '=', $id)->first())
						{
							$model->$relation()->attach($id);
						}
					}
				break;

				case 'remove' :
					
				break;

				default: throw new \InvalidArgumentException(get_class($model).'::'.$relation . '-' . 'operation');
			}
		};
	}

	public function hasManyRelationProcessor()
	{

	}

	public function hasOneRelationProcessor()
	{

	}

	public function morphManyRelationProcessor()
	{

	}

	public function morphOneRelationProcessor()
	{
		
	}

	public function run()
	{
		$data = $this->processedData();
		
		$updating = $this->model->exists;

		foreach($this->fields as $f)
		{
			if($updating and ! isset($data[$f]))
				continue;

			$value = isset($data[$f]) ? $data[$f] : null;

			if(isset($this->fieldProcessors[$f]))
			{
				$processor = $this->fieldProcessors[$f];
			}
			else
			{
				$processor = $this->defaultFieldProcessor;
			}

			$processor($this->model, $f, $value);
		}

		$this->model->save();

		
		foreach($this->relations as $r)
		{
			if($updating and ! isset($data[$r]))
				continue;

			$value = isset($data[$r]) ? $data[$r] : null;

			if(isset($this->relationProcessors[$r]))
			{
				$processor = $this->relationProcessors[$r];
			}
			else
			{
				$processor = $this->stdRelationProcessor($r);
			}

			$processor($this->model, $r, $value);
		}

		return $this->model;
	}

}