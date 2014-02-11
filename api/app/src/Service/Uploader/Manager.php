<?php namespace Service\Uploader;

use League\Flysystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Validation;
use Illuminate\Support\Str;

class Manager {

	const typeField = 'type';

	protected $fs;
	protected $httpClient;
	protected $urlGenerator;
	protected $validatorFactory;

	public function __construct(Filesystem $fs, HttpClient $httpClient, UrlGenerator $urlGenerator, Validation\Factory $validatorFactory)
	{
		$this->fs = $fs;
		$this->httpClient = $httpClient;
		$this->urlGenerator = $urlGenerator;
		$this->validatorFactory = $validatorFactory;
	}

	public static function validMediaTypes()
	{
		return ['audio', 'video', 'photo'];
	}

	const audioFlag = 'audio';
	const videoFlag = 'video';
	const photoFlag = 'photo';

	public function newValidator(Request $req)
	{
		$data = ['request' => $req];

		$rules = ['request' => 'required|valid_type|valid_source'];

		$val = $this->validatorFactory->make($data, $rules);

		$val->addExtension('valid_type', function () use ($req) {
			return in_array($req->input('type'), static::validMediaTypes());
		});

		$val->addExtension('valid_source', function () use ($req) {
			if(! in_array($req->input('type'), static::validMediaTypes()))
			{	
				// we don't want shit to fail when valid type rule has not been satisfied
				return true;
			}

			if($req->input('type') === static::photoFlag)
			{
				// when looking for photo input, we first look for
				// file upload
				if($req->file('source') and strpos($req->file('source')->getMimeType(), 'image') === 0)
				{
					return true;
				}

				// then we check for a url input
				if(
					$req->input('source') and filter_var($req->input('source'), FILTER_VALIDATE_URL) !== false and
					$res = $this->httpClient->get($req->input('source')) and
					$res->getStatusCode() == 200 and strpos($res->getContentType(), 'image') === 0
				)
				{
					return true;
				}
			}
			else
			{
				// we are here means that we need to validate things that are audio and video sources
				// for now we just check for a valid url and a valid provider
				// May expand later for more conditions
				
				if($req->input('source'))
				{
					if($req->input('type') === static::videoFlag)
					{
						return in_array($req->input('provider'), Drivers\Video::processors());
					}
					elseif($req->input('type') === static::audioFlag)
					{
						return in_array($req->input('provider'), Drivers\Audio::processors());
					}
				}
			}

			// if nothing works return false
			return false;
		});

		return $val;
	}

	protected function createPhotoDriver()
	{
		return new Drivers\Photo($this->fs, $this->httpClient, $this->urlGenerator);
	}

	protected function createVideoDriver()
	{
		return new Drivers\Video($this->httpClient);
	}

	protected function createAudioDriver()
	{
		return new Drivers\Audio($this->httpClient);
	}

	protected function driver($type)
	{
		return $this->{'create'.Str::studly($type).'Driver'}();
	}

	public function process(Request $req)
	{
		// we assume that the request has been validated by our very own validator
		// and move forward with things
		
		// driver processes the request and returns a "resource",
		// ie a Service\Uploader\Resource object
		// for the media that is our system understands

		return $this->driver($req->input('type'))->process($req);
	}

}