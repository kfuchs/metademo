<?php namespace k\Reposed\Tools;

use k\Reposed\Model;
use Illuminate\Support\MessageBag;
use Closure;

class Deletor {

	protected $model;
	protected $deletor;
	protected $preReqs;
	protected $deletionTerms;

	public function __construct(Model $model = null)
	{
		$this->model = $model;

		$this->deletor = function ($model) {
			return $model->delete();
		};

		$this->preReqs = function ($model) {
			return true;
		};

		$this->deletionTerms = new MessageBag;
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function setDeletor(Closure $callable)
	{
		$this->deletor = $callable;
		return $this;
	}

	public function getDeletor()
	{
		return $this->deletor;
	}

	public function setPreReqs(Closure $callable)
	{
		$this->preReqs = $callable;
		return $this;
	}

	public function getPreReqs()
	{
		return $this->preReqs;
	}

	public function setDeletionTerms($terms)
	{
		if($terms instanceof MessageBag)
		{
			$this->deletionTerms = $terms;
			return $this;
		}
		else
		{
			$this->deletionTerms = new MessageBag((array) $terms);
			return $this;
		}
	}

	public function getDeletionTerms()
	{
		return $this->deletionTerms;
	}

	public function run()
	{
		$preReqs = $this->preReqs;
		$deletor = $this->deletor;
		if(! $preReqs($this->model))
			return false;

		$deletor($this->model);
		return true;
	}

}