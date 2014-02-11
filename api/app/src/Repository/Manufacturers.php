<?php namespace Repository;

use Abstracts;

class Manufacturers extends Abstracts\Repository {

	public function forApiListing()
	{
		return $this;
	}

	public function forApiResource()
	{
		return $this;
	}

}