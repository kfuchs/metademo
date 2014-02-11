<?php namespace Service\Uploader\Drivers;

use League\Flysystem\Filesystem;
use Service\Uploader\HttpClient;
use Service\Uploader\UrlGenerator;
use Illuminate\Http\Request;

class Photo extends Base {

	protected $fs;
	protected $httpClient;
	protected $urlGenerator;

	public function __construct(Filesystem $fs, HttpClient $httpClient, UrlGenerator $urlGenerator)
	{
		$this->fs = $fs;
		$this->httpClient = $httpClient;
		$this->urlGenerator = $urlGenerator;
	}

	protected function createFileProcessor()
	{
		return new Processors\Photo\File($this->fs, $this->urlGenerator);
	}

	protected function createUrlProcessor()
	{
		return new Processors\Photo\Url($this->fs, $this->httpClient, $this->urlGenerator);
	}

	public function process(Request $req)
	{
		if($req->file('source'))
		{
			return $this->createFileProcessor()->run($req);
		}
		elseif($req->input('source'))
		{
			return $this->createUrlProcessor()->run($req);
		}
		else
		{
			throw new \InvalidArgumentException(static::class.'::'.__FUNCTION__);
		}
	}

}