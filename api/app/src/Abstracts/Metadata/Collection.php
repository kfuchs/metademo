<?php namespace Abstracts\Metadata;

use Abstracts;

class Collection extends Abstracts\Collection {

	public static function makeFromData(array $data = array())
	{
		$instance = new static;
		$instance->fill($data);

		return $instance;
	}

	public function markExists($mark)
	{
		foreach($this->items as $item)
		{
			$item->exists = $mark;
		}

		return $this;
	}

	public function fill(array $data = array())
	{
		foreach($data as $field => $value)
		{
			if($item = $this->getItemByField($field))
			{
				$this->remove($item);
			}

			$this->items[] = new Model(['field' => $field, 'value' => $value]);
		}
	}

	public function persist()
	{
		foreach($this->items as $item)
		{
			if(! $item->exists or $item->getDirty())
				$item->save();
		}
	}

	public function getItemByField($field)
	{
		foreach($this->items as $item)
		{
			if($item->field === $field)
			{
				return $item;
			}
		}

		return null;
	}

	public function getValueByField($field)
	{
		foreach($this->items as $item)
		{
			if($item->field === $field)
			{
				return $item->value;
			}
		}	

		return null;
	}

	public function remove(Model $metadata)
	{
		foreach($this->items as $i => $item)
		{
			if($item->id === $metadata->id)
			{
				unset($this->items[$i]);
				break;
			}
		}

		$this->items = array_values($this->items);
	}

	// a helper for $key => $val access of content properties	
	public function __get($key)
	{
		$field = snake_case($key);

		return $this->getValueByField($field);
	}

}