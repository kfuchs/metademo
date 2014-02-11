<?php namespace Abstracts;

use Illuminate\Routing;
use Illuminate\Validation\Validator;
use Illuminate\Support\MessageBag;
use Response;
use Route;
use DB;
use Illuminate\Routing\Router;
use Illuminate\Container\Container;
use Illuminate\Database\DatabaseManager;

abstract class Controller extends Routing\Controller {

	protected $bindings = array();

	protected $transactionals = array();

	public function __construct()
	{
		$this->setupBindings();
		$this->setup();
	}

	public static function action($name)
	{
		return static::class.'@'.$name;
	}

	// setup bound parameters in controllers context
	protected function setupBindings()
	{
		foreach($this->bindings as $param => $method)
		{
			$this->bindings[$param] = $this->$method(Route::getCurrentRoute()->getParameter($param));
		}
	}

	protected function setup()
	{
		//override as needed
	}

	// get parameters binded in controllers context
	public function binding($key, $default = null)
	{
		return isset($this->bindings[$key]) ? $this->bindings[$key] : $default;
	}

	public function __get($attr)
	{
		return $this->binding($attr);
	}

	public function getTransactionals()
	{
		return $this->transactionals;
	}

	public function isTransactional($method)
	{
		return in_array($method, $this->getTransactionals());
	}

	/**
	 * Execute an action on the controller.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function callAction($method, $parameters)
	{
		if($this->isTransactional($method))
		{
			return DB::transaction(function () use ($method, $parameters) {
				return parent::callAction($method, $parameters);
			});
		}
		else
		{
			return parent::callAction($method, $parameters);
		}
	}
	
}