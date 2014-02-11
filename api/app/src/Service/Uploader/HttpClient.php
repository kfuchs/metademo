<?php namespace Service\Uploader;

use Closure;
use Guzzle\Http;

class HttpClient {

	protected $reqFactory;

	public function __construct(Closure $reqFactory)
	{
		$this->reqFactory = $reqFactory;
	}

	public function get($url)
	{
		$reqFactory = $this->reqFactory;
		$req = $reqFactory($url)->get();

		try
		{
			return $req->send();
		}
		catch(Http\Exception\HttpException $e)
		{
			return $e->getResponse();
		}
	}

}