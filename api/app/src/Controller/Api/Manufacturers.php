<?php namespace Controller\Api;

use Abstracts\ResourceController;
use Core\Manufacturer;

class Manufacturers extends ResourceController {

	protected function repo()
	{
		return Manufacturer::repo();
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