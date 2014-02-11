<?php namespace Abstracts;

use Input;
use Response;
use k\Reposed\Tools\Serializer;

abstract class ResourceController extends Controller {

	protected $usesPagination = false;
	protected $perPage = 20;

	protected $identifier = 'id';

	protected $requiredBindings = null;

	protected $transactionals = ['create', 'update', 'destroy'];

	protected $formats = [];

	public function __construct()
	{
		parent::__construct();

		$this->beforeFilter(function () {
			return $this->requiredBindingsFilter();
		});
	}

	protected function repo()
	{

	}

	public function isPaginated()
	{
		if(Input::has('_pagination'))
		{
			return Input::get('_pagination') === 1;
		}
		else
		{
			return $this->usesPagination;
		}
	}

	public function makePaginated()
	{
		$this->usesPagination = true;
		return $this;	
	}

	public function makeUnPaginated()
	{
		$this->usesPagination = false;
		return $this;
	}

	public function getPerPage()
	{
		$perPage = Input::get('_per_page', $this->perPage);

		if($perPage < 5) return 5;
		if($perPage > 100) return 50;

		return $this->perPage;
	}

	public function setPerPage($perPage)
	{
		$this->perPage = $perPage;
		return $this;
	}

	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
		return $this;
	}

	public function getIdentifier()
	{
		return $this->identifier;
	}

	public function requiredBindingsFilter()
	{
		if(is_array($this->requiredBindings))
			$requiredBindings = $this->requiredBindings;
		else
			$requiredBindings = array_keys($this->bindings);

		foreach($requiredBindings as $b)
		{
			if($this->binding($b) === null)
			{
				return $this->notFoundResponse();
			}
		}
	}

	protected function getFormats()
	{
		return $this->formats;
	}

	protected function getFormat($key)
	{
		$formats = $this->getFormats();
		
		return isset($formats[$key]) ? $formats[$key] : [];
	}

	protected function newSerializer($fields = array())
	{
		return (new Serializer)->setFields($fields);
	}

	protected function getSerializer()
	{
		return $this->newSerializer($this->getFormat(Input::get('_format')));
	}

	/**
	 * Should return appropriately scoped repository for lists
	 * @return Reposed\Repository
	 */
	protected function listingProvider()
	{
		return $this->repo();
	}

	/**
	 * Should return appropriately scoped repository for resource
	 * @return Reposed\Repository
	 */
	protected function resourceProvider()
	{
		return $this->repo();
	}

	
	/** default provided actions **/
	public function index()
	{
		$f = $this->repo()->newFilterer(Input::get('filters', []));
		$s = $this->repo()->newSorter(Input::get('sortings', []));

		$repo = $this->listingProvider()->applyFilters($f)->applySortings($s);

		if($this->isPaginated())
			return Response::paginator($repo->paginate($this->getPerPage()), [], $this->getSerializer());
		else
			return Response::collection($repo->get(), [], $this->getSerializer());
	}

	public function count()
	{
		$f = $this->repo()->newFilterer(Input::get('filters', []));

		$repo = $this->listingProvider()->applyFilters($f);

		return Response::msg($repo->count());
	}

	public function show()
	{
		$args = func_get_args(); $id = end($args);

		if(! $m = $this->resourceProvider()->find($this->identifier, $id))
			return Response::notFound();

		return Response::model($m, [], $this->getSerializer());
	}

	public function create()
	{
		$val = $this->repo()->newValidator(Input::get());

		if($val->fails())
			return Response::errors($val);

		$m = $this->repo()->newPersistor(Input::get())->run();

		return Response::action($this->resourceProvider()->find($m->id), 'Created', [], $this->getSerializer());
	}

	public function update()
	{
		$args = func_get_args(); $id = end($args);

		if(! $m = $this->resourceProvider()->find($this->identifier, $id))
			return Response::notFound();

		$val = $m->newValidator(Input::get());

		if($val->fails())
			return Response::errors($val);

		$m->newPersistor(Input::get())->run();

		return Response::action($this->resourceProvider()->find($m->id), 'Updated', [], $this->getSerializer());
	}

	public function destroy()
	{
		$args = func_get_args(); $id = end($args);

		if(! $m = $this->resourceProvider()->find($this->identifier, $id))
			return Response::notFound();

		// Some Bullshit Should go here
		
		$m->delete();
		
		return Response::action($m, 'Destroyed', [], $this->getSerializer());
	}

}