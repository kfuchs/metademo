<?php namespace Core;

use Abstracts;
use Repository;
use Validator;
use k\Reposed\Tools;

class Manufacturer extends Abstracts\Model {

	/**
	 * settings
	 */
	protected $table = 'manufacturers';

	public function newRepository()
	{
		return new Repository\Manufacturers($this);
	}

	/**
	 * relations
	 */
	public function widgets()
	{
		return $this->hasMany(Widget::class, 'manufacturer_id');
	}

	/**
	 * validator
	 */
	public function newValidator(array $data = array())
	{
		$rules = [
			'name' => 'required|max:255|unique:'.static::table().',name,'.(int)$this->id,
			'about' => 'required|max:500'
		];

		$val = Validator::make($data, $rules);

		return $val;
	}

	/**
	 * persistor
	 */
	public function newPersistor(array $data = array())
	{
		return (new Tools\Persistor($this))
					->setData($data)
					->setFields(['name', 'about']);
	}

}