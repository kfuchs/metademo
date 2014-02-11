<?php namespace Tools;

use Abstracts;
use Str;
use Closure;

class Slugger
{
	protected $model = null;
	protected $titleField = 'name';
	protected $slugField = 'slug';

	protected static $_check = null;
	protected static $_step = null;

	public static function make($model, $titleField = null, $slugField = null)
	{
		return new static($model, $titleField, $slugField);
	}

	public function __construct($model, $titleField = null, $slugField = null)
	{
		$this->model = $model;
		$titleField !== null and $this->titleField = $titleField;
		$slugField !== null and $this->slugField = $slugField;
	}

	public static function check()
	{
		if(static::$_check !== null)
			return static::$_check;

		return static::$_check = function ($self, $slug) {
			$match = $self->getModel()->newRepository()->find($self->getSlugField(), $slug);
			return $match === null or (int) $match->id === (int) $self->getModel()->id;
		};
	}

	public static function step()
	{
		if(static::$_step !== null)
			return static::$_step;

		return static::$_step = function ($self, $suffix, $separator) {
			return Str::slug($self->getModel()->getAttribute($self->getTitleField()).' '.$suffix, $separator);
		};
	}

	public function setModel(Abstracts\Model $model)
	{
		$this->model = $model;
		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function setTitleField($field = null)
	{
		$this->titleField = $field;
		return $this;
	}

	public function getTitleField()
	{
		return $this->titleField;
	}

	public function setSlugField($field)
	{
		$this->slugField = $field;
		return $this;
	}

	public function getSlugField()
	{
		return $this->slugField;
	}

	public function slugify($separator = '-', Closure $check = null, Closure $step = null)
	{
		$suffix = 1;
		$check = $check ? $check : static::check();
		$step = $step ? $step : static::step();
		
		$slug = $step($this, null, $separator);

		while(! $check($this, $slug) )
		{
			$slug = $step($this, $suffix, $separator);
			$suffix++;
		}

		$model = $this->getModel();

		$model->setAttribute($this->slugField, $slug);

		return $this;
	}
}