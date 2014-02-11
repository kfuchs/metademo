<?php namespace k\Reposed\Tools;

use k\Reposed\Collection;
use k\Reposed\Model;
use Illuminate\Support\Contracts\ArrayableInterface;
use InvalidArgumentException;
use Closure;

class Serializer {

	protected $fields = array();

	public function setFields(array $fields)
	{
		$this->fields = $this->processFields($fields);
		return $this;
	}

	public function getFields()
	{
		return $this->fields;
	}

	protected function processFields(array $fields)
	{
		return $this->breakDotsToNested($fields);
	}

	protected function breakDotsToNested(array $fields)
	{
		$nesteds = array();

		foreach($fields as $k => $v)
		{
			if($v instanceof Closure or is_int($k))
			{
				if(! $v instanceof Closure and strpos($v, '+') !== 0 and strpos($v, '-') !== 0)
				{
					throw new InvalidArgumentException(
						'Field: ' . $k . ' : ' . $fields[$k] instanceof Closure ? 'Closure' : $fields[$k]
					);
				}

				if($v instanceof Closure)
				{
					$v = $k;
				}

				$parts = explode('.', $v);
				if(count($parts) > 1)
				{
					$operator = $parts[0][0];
					$property = substr($parts[0], 1);
					$subProperty = $operator . implode('.', array_slice($parts, 1));
					$field = array($property => $subProperty);
					$nesteds = array_merge_recursive($nesteds, $field);
					unset($fields[$k]);
				}
			}
			else
			{
				if(strpos($k, '+') === 0 or strpos($k, '-') === 0)
				{
					throw new InvalidArgumentException('Field: ' . $k);
				}
			}
		}

		return array_merge_recursive($fields, $nesteds);
	}

	public function run($data)
	{
		if(! is_object($data) and ! is_array($data))
		{
			return $data;
		}

		switch ($data) {
			case $data instanceof Collection         : return $this->serializeCollection($data);
			case $data instanceof Model              : return $this->serializeModel($data);
			case $data instanceof ArrayableInterface : return $this->run($data->toArray());
			case is_array($data)					 : return $this->serializeArray($data);
			default                                  : return $data;
		}
	}

	protected function serializeCollection(Collection $items)
	{
		return $items->map(function ($item) {
			return $this->run($item);
		})->all();
	}

	protected function serializeArray(array $data)
	{
		return array_map(function ($el) {
				return $this->run($el); 
		}, $data);
	}

	public function serializables(Model $model)
	{
		$fields = array();
		$processableFields = [];

		foreach ($this->fields as $k => $v)
		{
			if(is_int($k))
			{
				$fields[] = $v;
			}
			else
			{
				$fields[] = $k;

				if($v instanceof Closure)
				{
					$processableFields[] = $k;
				}
			}
		}

		$added = array_values(array_filter($fields, array($this, 'addedFilter')));
		$added = array_map(array($this, 'addedFieldName'), $added);

		$removed = array_values(array_filter($fields, array($this, 'removedFilter')));
		$removed = array_map(array($this, 'removedFieldName'), $removed);

		$serializables = $model->getSerializables();

		$serializables = array_merge($serializables, $added);
		$serializables = array_values(array_diff($serializables, $removed));

		foreach($serializables as $i => $serializable)
		{
			if(in_array($serializable, $processableFields))
			{
				$serializables[$i] = [$serializable, $this->fields[$serializable]];
			}
		}

		return $serializables;
	}

	protected function addedFilter($field)
	{
		return strpos($field, '+') === 0;
	}

	protected function addedFieldName($field)
	{
		return substr($field, 1);
	}

	protected function removedFilter($field)
	{
		return strpos($field, '-') === 0;
	}

	protected function removedFieldName($field)
	{
		return substr($field, 1);
	}

	public function nestedFields($head)
	{
		if(isset($this->fields[$head]))
		{
			return (array) $this->fields[$head];
		}
		else
		{
			return array();
		}
	}

	protected function newNested($head)
	{
		$s = new static;
		$s->setFields($this->nestedFields($head));
		return $s;
	}

	protected function serializeModel(Model $model) 
	{
		$result = array();

		foreach($this->serializables($model) as $serializable)
		{
			$serializable = (array) $serializable;
			
			$field = array_shift($serializable);
			$processor = array_shift($serializable);			

			if(in_array($field, $model->getSerializedFields()))
			{
				$serializingMethod = 'serialize' . studly_case($field);
				$val = $this->newNested($field)->run($model->$serializingMethod());
			}
			else
			{
				$val = $this->newNested($field)->run($model->getAttribute($field));
			}

			if($processor instanceof Closure)
			{
				$val = $processor($val);
			}

			$result[$field] = $val;
		}

		return $result;
	}

}