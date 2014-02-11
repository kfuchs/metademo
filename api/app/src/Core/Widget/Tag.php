<?php namespace Core\Widget;

use Abstracts;
use Widget;
use Repository;
use Validator;
use k\Reposed\Tools;

class Tag extends Abstracts\Model {

	/**
	 * settings
	 */
	protected $table ='widget_tags';

	public function newRepository()
	{
		return new Repository\Widget\Tags($this);
	}

	/**
	 * relations
	 */
	public function widgets()
	{
		return $this->belongsToMany(
			Widget::class, 'widget_widget_tag', 'tag_id', 'widget_id'
		);
	}

	/**
	 * validator
	 */
	public function newValidator(array $data = array())
	{
		$rules = [
			'name' => 'required|max:255|unique:'.static::table().',name,'.(int)$this->id
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
					->setFields(['name']);
	}

}