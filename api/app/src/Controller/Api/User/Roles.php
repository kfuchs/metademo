<?php namespace Controller\Api\User;

use Abstracts\ResourceController;
use Core\User\Role;

class Roles extends ResourceController {

	protected function repo()
	{
		return Role::repo();
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