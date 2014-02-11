<?php namespace Service\Uploader;

class Resource {

	protected $url;
	protected $provider;
	protected $metadata;

	public function __construct($url, $provider, $metadata = null)
	{
		$this->url = $url;
		$this->provider = $provider;
		$this->metadata = $metadata; 
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function getProvider()
	{
		return $this->provider;
	}

	public function getMetadata()
	{
		return $this->metadata;
	}

}