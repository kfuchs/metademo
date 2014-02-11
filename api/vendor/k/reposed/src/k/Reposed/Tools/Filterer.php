<?php namespace k\Reposed\Tools;

use Closure;
use DateTime;
use k\Reposed\Repository;

class Filterer {

	protected $dates = array();
	protected $filterables = array();
	protected $filters = array();
	protected $params = array();
	protected $paramProcessors = array();

	public function getDates()
	{
		return $this->dates;
	}

	public function setDates(array $dates)
	{
		$this->dates = $dates;
		return $this;
	}

	public function appendDates($fields)
	{
		$fields = (array) $fields;
		$this->dates = array_merge($this->dates, $fields);
		
		return $this;
	}


	public function setFilterables(array $filterables)
	{
		$this->filterables = $filterables;
		return $this;
	}

	public function getFilterables()
	{
		return $this->filterables;
	}

	public function setFilter($fields, Closure $filter)
	{
		$fields = (array) $fields;
		
		foreach($fields as $field)
		{
			$this->filters[$field] = $filter;
		}
		
		return $this;
	}

	public function getFilter($field)
	{
		return isset($this->filters[$field]) ? $this->filters[$field] : null;
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

	public function setParamProcessor($params, Closure $processor)
	{
		$params = (array) $params;

		foreach($params as $param)
		{
			$this->paramProcessors[$param] = $processor;
		}

		return $this;
	}

	public function getParamProcessor($param)
	{
		return isset($this->paramProcessors[$param]) ? $this->paramProcessors[$param] : null;
	}

	protected function strToDateTime($str)
	{
		$date = new DateTime;
		$date->setTimestamp((int) strtotime($str));

		return $date;
	}

	protected function processParam($name, $value)
	{
		if($processor = $this->getParamProcessor($name))
		{
			return $processor($name, $value);
		}
		
		if(is_array($value))
		{
			foreach($value as $i => $v)
			{
				if(in_array($name, $this->dates))
				{
					$v = $this->strToDateTime($v);
				}
				
				$value[$i] = $v;
			}
		}
		elseif(in_array($name, $this->dates))
		{
			$value = $this->strToDateTime($value);
		}

		return $value;
	}

	public function processedParams()
	{
		// In this method we go through the params the filter is provided
		// and convert the date fields to arrays . The parameter array is
		// treated as something which can have at max one nesting level and
		// has its primary keys only in the top level

		$processed = array();

		foreach($this->params as $k => $v)
		{
			$processed[$k] = $this->processParam($k, $v);
		}

		return $processed;
	}

	public function processedFilterables()
	{
		$processed = array();

		foreach($this->filterables as $k => $v)
		{
			if(is_numeric($k))
			{
				$k = $v;
				$v = $v;
			}
			
			$processed[$k] = $v;
		}

		return $processed;
	}

	public function stdFilter(Repository $repo, $field, $param)
	{
		return $repo->newScoped(function ($q) use ($field, $param) {
			$q->where($field, 'like', '%'.trim($param).'%');
		});
	}

	public function rangeFilter(Repository $repo, $field, array $param)
	{
		return $repo->newScoped(function ($q) use ($field, $param) {
			($min = reset($param)) and $q->where($field, '>=', trim($min));
			($max = end($param)) and $q->where($field, '<=', trim($max));
		});
	}

	public function run(Repository $repo)
	{
		$filterables = $this->processedFilterables();
		$params = $this->processedParams();

		foreach($filterables as $alias => $field)
		{
			if(! isset($params[$alias]) ) continue;
			$param = $params[$alias];

			if(array_key_exists($alias, $this->filters))
			{
				//how to handle stuff when we have custom filters
				$filter = $this->filters[$alias];
				$repo = $filter($repo, $field, $param);
				continue;
			}
			
			// the default handling of stuff
			// if the supplied parameter is array, we'll just look
			// at first and last elements and apply a >=first and <=last condition
			if(is_array($param))
			{
				$repo = $this->rangeFilter($repo, $field, $param);
			}
			else
			{
				$repo = $this->stdFilter($repo, $field, $param);
			}
		}

		return $repo;
	}
}