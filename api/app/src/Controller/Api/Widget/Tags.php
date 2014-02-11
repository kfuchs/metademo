<?php namespace Controller\Api\Widget;

use Abstracts\ResourceController;
use Core\Widget\Tag;

class Tags extends ResourceController {

	protected function repo()
	{
		return Tag::repo();
	}

	protected function listingProvider()
	{
		return $this->repo()->forApiListing();
	}

	protected function resourceProvider()
	{
		return $this->repo()->forApiResource();
	}

}