<?php namespace Service\Uploader\Drivers\Processors\Photo;

use League\Flysystem\Filesystem;
use Service\Uploader\HttpClient;
use Service\Uploader\UrlGenerator;
use Service\Uploader\Resource;
use Illuminate\Http\Request;

class Url {

	protected $fs;
	protected $httpClient;
	protected $urlGenerator;

	public function __construct(Filesystem $fs, HttpClient $httpClient, UrlGenerator $urlGenerator)
	{
		$this->fs = $fs;
		$this->httpClient = $httpClient;
		$this->urlGenerator = $urlGenerator;
	}

	public function run(Request $req)
	{
		$url = $req->input('source');
		$res = $this->httpClient->get($url);

		$ext = substr($res->getContentType(), strlen('image/'));

		$fname = md5($url . microtime(true)).'.'.$ext;

		$this->fs->put($fname, (string) $res->getBody());

		return new Resource($this->urlGenerator->to($fname), 'url');
	}

}