<?php namespace k\EAV\Metadata;

use k\Reposed;

class Collection extends Reposed\Collection {

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
			if($item->field === $key)
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