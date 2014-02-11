<?php namespace k\Reposed\Tools;

use k\Reposed\Repository;

class Sorter {

	protected $sortables = array();
	protected $params = array();

	public function setSortables(array $sortables)
	{
		$this->sortables = $sortables;
		return $this;
	}

	public function getSortables()
	{
		return $this->sortables;
	}

	public function setParams(array $params)
	{
		$this->params = $params;
		return $this;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function processedSortables()
	{
		$processed = array();

		foreach($this->sortables as $k => $v)
		{
			if(is_numeric($k))
			{
				$k = $v;
			}
			
			$processed[$k] = $v;
		}

		return $processed;
	}

	public function run(Repository $repo)
	{
		$params = $this->params;
		$sortables = $this->processedSortables();

		foreach ($sortables as $alias => $field)
		{
			if(! isset($params[$alias]) ) continue;
			$param = $params[$alias];

			if(in_array($param, array('asc', 'desc')))
				$repo = $repo->newScoped(function ($q) use ($field, $param) { $q->orderBy($field, $param); });
		}

		return $repo;
	}

}