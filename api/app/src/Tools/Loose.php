<?php namespace Tools;

use Closure;
use BadMethodCallException;

class Loose {

	protected $properties = array();

	public function get($prop)
	{
		return isset($this->properties[$prop]) ? $this->properties[$prop] : null;
	}

	public function set($prop, $val)
	{
		$this->properties[$prop] = $val;
		return $this;
	}

	public function __get($prop)
	{
		return $this->get($prop);
	}

	public function __set($prop, $val)
	{
		return $this->set($prop, $val);
	}

	public function __call($method, $args = array())
	{
		$closure = $this->get($method);

		if(! $closure instanceof Closure)
		{
			throw new BadMethodCallException(get_class($this) . '::' . $method);
		}

		return call_user_func_array($closure, array_merge(array($this), $args));
	}

}