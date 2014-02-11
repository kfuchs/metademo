<?php namespace Abstracts;

use k\Reposed;
use k\Reposed\Tools;

class Repository extends Reposed\Repository {

	public function scopeActive()
	{
		return $this->newScoped(function ($q) {
			$q->where($this->c('is_active'), '=', 1);
		});
	}

	public function scopeInactive()
	{
		return $this->newScoped(function ($q) {
			$q->where($this->c('is_active'), '!=', 1);
		});
	}

	/**
	 * Adding default newFilterer and newOrderer methods
	 * to get shit working
	 */	
	public function newFilterer(array $params = array())
	{
		return new Tools\Filterer;
	}

	public function newSorter(array $params = array())
	{
		return new Tools\Sorter;
	}

	public function scopeStartsWith($field, $value)
	{
		return $this->newScoped(function ($q) use($field, $value) {
			$q->where($field, 'LIKE', "{$value}%");
		});
	}

}
