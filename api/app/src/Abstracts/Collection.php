<?php namespace Abstracts;

use k\Reposed;

class Collection extends Reposed\Collection {

	/**
	 * Get one or more items at random from the collection.
	 *
	 * @param  int  $amount
	 * @return mixed
	 */
	public function random($amount = 1)
	{
		$keys = array_rand($this->items, $amount);
		return is_array($keys) ? array_intersect_key($this->items, array_flip($keys)) : $this->items[$keys];
	}

}
