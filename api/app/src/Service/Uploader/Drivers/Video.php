<?php namespace Service\Uploader\Drivers;

use Service\Uploader\HttpClient;

class Video extends Base {

	protected $httpClient;

	public function __construct(HttpClient $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function createYoutubeProcessor()
	{
		return new Processors\Video\Youtube($this->httpClient);
	}

}