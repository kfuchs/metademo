<?php namespace Service\Uploader\Drivers\Processors\Video;

use Service\Uploader\HttpClient;
use Illuminate\Http\Request;
use Service\Uploader\Resource;

class Youtube {

	protected $httpClient;

	public function __construct(HttpClient $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function run(Request $req)
	{
		return new Resource($req->input('source'), 'youtube');
	}

}