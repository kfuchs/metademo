<?php namespace Repository\User;

use Abstracts;

class Roles extends Abstracts\Repository {

	public function forApiListing()
	{
		return $this;
	}

	public function forApiResource()
	{
		return $this;
	}

}