<?php namespace Service\Uploader\Drivers\Processors\Photo;

use League\Flysystem\Filesystem;
use Service\Uploader\UrlGenerator;
use Service\Uploader\Resource;
use Illuminate\Http\Request;

class File {

	protected $fs;
	protected $urlGenerator;

	public function __construct(Filesystem $fs, UrlGenerator $urlGenerator)
	{
		$this->fs = $fs;
		$this->urlGenerator = $urlGenerator;
	}

	public function run(Request $req)
	{
		$file = $req->file('source');
		$fname = md5($file->getClientOriginalName() . microtime(true)).'.'.$file->guessExtension();

		$this->fs->put($fname, file_get_contents($file->getRealPath()));

		return new Resource($this->urlGenerator->to($fname), 'file');
	}

}