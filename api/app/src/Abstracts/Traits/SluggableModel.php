<?php namespace Abstracts\Traits;

use Tools\Slugger;
use Closure;

trait SluggableModel {

	public function slugify($separator = '-', Closure $check = null, Closure $step = null)
	{
		Slugger::make($this)->slugify($separator, $check, $step);
	}

	public function save(array $options = array())
	{
		$this->slugify();
		return parent::save($options);
	}

}