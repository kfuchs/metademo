<?php namespace Repository;

use Abstracts;
use Core\Manufacturer;
use Core\Widget\Tag;
use Abstracts\Collection;
use k\Reposed\Tools;

class Widgets extends Abstracts\Repository {

	protected function joinTagsPivot()
	{
		return $this->newJoint(function ($q) {
			$pivot = $this->tags()->getTable();
			$q->join($pivot, $pivot.'.widget_id', '=', $this->c('id'));
		});
	}

	protected function leftJoinTagsPivot()
	{
		return $this->newJoin(function ($q) {
			$pivot = $this->tags()->getTable();
			$q->leftJoin($pivot, $pivot.'.widget_id', '=', $this->c('id'));
		});
	}

	protected function joinTags()
	{
		return $this->joinTagsPivot()->newJoint(function ($q) {
			$pivot = $this->tags()->getTable();
			$q->join(Tag::table(), $pivot.'.tag_id', '=', Tag::c('id'));
		});
	}

	protected function leftJoinTags()
	{
		return $this->leftJoinTagsPivot()->newJoint(function ($q) {
			$pivot = $this->tags()->getTable();
			$q->leftJoin(Tag::table(), $pivot.'.tag_id', '=', Tag::c('id'));
		});
	}

	/////
	
	protected function withManufacturer()
	{
		return $this->newScoped(function ($q) {
			$q->with('manufacturer');
		});
	}

	protected function withTags()
	{
		return $this->newScoped(function ($q) {
			$q->with('tags');
		});
	}

	/////

	public function forManufacturer(Manufacturer $m = null)
	{
		return $this->newScoped(function ($q) use ($m) {
			if($m) $q->where($this->c('manufacturer_id'), '=', $m->id);
			else   $q->whereNull($this->c('id'));
		});
	}

	public function forTag(Tag $tag = null)
	{
		return $this->joinTagsPivot()->newScoped(function ($q) use ($tag) {
			$pivot = $this->tags()->getTable();
			if($tag) $q->where($pivot.'.tag_id', '=', $tag->id);
			else     $q->whereNull($this->c('id'));
		});
	}

	public function notForTag(Tag $tag = null)
	{
		return $this->leftJoinTagsPivot()->newScoped(function ($q) use ($tag) {
			$pivot = $this->tags()->getTable();
			if($tag) $q->where($pivot.'.tag_id', '!=', $tag->id)->orWhereNull($pivot.'.id');
			else     $q->whereNull($this->c('id'));
		});
	}

	public function forTags(Collection $tags)
	{
		return $this->joinTagsPivot()->newScoped(function ($q) use ($tags) {
			$pivot = $this->tags()->getTable();
			if($tags->count() > 0) $q->whereIn($pivot.'.tag_id', $tags->modelKeys());
			else                   $q->whereNull($this->c('id'));
		});
	}

	/////
	
	public function newFilterer(array $params = array())
	{
		$f = (new Tools\Filterer)->setParams($params);

		$f->setFilterables(['for_manufacturer']);

		$f->setFilter('for_manufacturer', function ($r, $f, $v) {
			$manufacturer = Manufacturer::repo()->find($v);
			return $this->forManufacturer($manufacturer);
		});

		return $f;
	}

	////
	
	public function forApiListing()
	{
		return $this->withManufacturer();
	}

	public function forApiResource()
	{
		return $this->withManufacturer()
					->withTags();
	}

}