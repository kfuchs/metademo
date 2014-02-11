<?php namespace Service\Uploader\Drivers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Service\Uploader\Resource;

abstract class Base {

	protected static $processorsCache = [];

	public static function processors()
	{
		$class = get_called_class();

		if(isset(static::$processorsCache[$class]) && is_array(static::$processorsCache[$class]))
		{
			return static::$processorsCache[$class];
		}

		static::$processorsCache[$class] = [];

		$methods = get_class_methods($class);

		foreach($methods as $method)
		{
			if (preg_match('/^create(.+)Processor$/', $method, $matches))
			{
				static::$processorsCache[$class][] = lcfirst($matches[1]);
			}
		}

		return static::processors();

	}

	protected function processor($slug)
	{
		$method = 'create'.Str::studly($slug).'Processor';
		
		if(method_exists($this, $method))
		{
			return $this->$method();
		}

		return null;
	}

	public function process(Request $req)
	{
		if($processor = $this->processor($req->input('provider')))
		{
			// again a processor run should return an
			// instanceof Service\Uploader\Resource
			return $processor->run($req);
		}
		else
		{
			return new Resource($req->input('source'));
		}
	}

}