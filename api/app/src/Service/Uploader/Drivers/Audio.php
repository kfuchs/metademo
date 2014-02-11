<?php namespace Service\Uploader\Drivers;

use Service\Uploader\HttpClient;

class Audio extends Base {

	protected $httpClient;

	public function __construct(HttpClient $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function createSoundcloudProcessor()
	{
		return new Processors\Audio\Soundcloud($this->httpClient);
	}

}