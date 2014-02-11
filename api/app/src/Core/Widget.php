<?php namespace Core;

use Abstracts;
use Repository;
use Core\Widget\Tag;
use Validator;
use k\Reposed\Tools;

class Widget extends Abstracts\Model {

	/**
	 * settings
	 */
	protected $table = 'widgets';

	public function newRepository()
	{
		return new Repository\Widgets($this);
	}

	/**
	 * relations
	 */
	public function manufacturer()
	{
		return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
	}

	public function tags()
	{
		return $this->belongsToMany(
			Tag::class, 'widget_widget_tag', 'widget_id', 'tag_id'
		);
	}

	/**
	 * validator
	 */
	public function newValidator(array $data = array())
	{
		$rules = [
			'manufacturer_id' => 'required|exists:'.Manufacturer::table().',id',
			'name' => 'required|max:255',
			'photo' => 'required|url',
		];

		return Validator::make($data, $rules);
	}

	public function newPersistor(array $data = array())
	{
		return (new Tools\Persistor($this))
					->setData($data)
					->setFields(['manufacturer_id', 'name', 'photo']);
	}

}