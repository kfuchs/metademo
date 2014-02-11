<?php namespace k\Reposed;

use Illuminate\Database\Eloquent;
use BadMethodCallException;
use Closure;
use Illuminate\Cache;
use Illuminate\Database\Query\Expression as DBExpr;

class Repository {

	protected $cache;

	protected $model;
	protected $scopes = array();
	protected $joints = array();
	protected $includedFields = array();

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function setCache(Cache\Repository $cache)
	{
		$this->cache = $cache;
		return $this;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function query()
	{
		$q = $this->model->newQuery();

		return $this->scopeQuery($q);
	}

	protected function scopeQuery(Eloquent\Builder $q)
	{
		foreach ($this->scopes as $scope)
		{
			$scope($q);
		}

		return $q;
	}

	public function getScopes()
	{
		return $this->scopes;
	}

	public function setScopes(array $scopes)
	{
		$this->scopes = $scopes;
		return $this;
	}

	public function addScope(Closure $scope)
	{
		$this->scopes[] = $scope;
		return $this;
	}

	public function getJoints()
	{
		return $this->joints;
	}

	public function setJoints(array $joints)
	{
		$this->joints = $joints;
		return $this;
	}

	public function addJoint($joint)
	{
		$this->joints[] = $joint;
		return $this;
	}

	protected function callStaticMethodOnModel($method, array $args = array())
	{
		$class = get_class($this->model);
		return call_user_func_array([$class, $method], $args);
	}

	/**
	 *  Use this one to add extra fields to the model
	 */
	public function includeFields(array $fields)
	{
		$allowedExtraFields = $this->getAllowedExtraFields();

		foreach($fields as $name => $field)
		{
			if(is_numeric($name))
			{
				list($name, $field) = array($field, $field);
			}
			else
			{
				list($name, $field) = array($name, $field);
			}

			if(in_array($name, $allowedExtraFields))
			{
				$this->includedFields[$name] = $field;
			}
		}

		return $this;
	}

	public function getIncludedFields()
	{
		return $this->includedFields;
	}

	public function rawInstance()
	{
		return new static($this->model);
	}

	/**
	 * Use this for chaining scopes. For example
	 *
	 * public function forUser($user)
	 * {
	 * 		return $this->newScoped(function ($q) use ($user) {
	 *   		return $q->where('userId', '=', $user->id);
	 * 		});
	 * }
	 * 
	 * @return Reposed\Repository
	 */
	public function newScoped(Closure $scope)
	{
		
		return $this->rawInstance()
					->setScopes($this->scopes)
					->setJoints($this->joints)
					->includeFields($this->includedFields)
					->addScope($scope);
	}

	/**
	 * Use this for joining tables, it smartly applies one type
	 * of join only once, no matter how many times you call it.
	 *
	 * public function joinPosts()
	 * {
	 * 		return $this->newJoint(function ($q) {
	 * 			$q->join('posts', 'posts.user_id', '=', $this->c('id'))
	 * 		});
	 * }
	 */
	public function newJoint(Closure $scope)
	{
		$trace = debug_backtrace(false);
		$joint = $trace[1]['function'];
		$args  = $trace[1]['args'];

		if(! empty($args))
		{
			throw new BadMethodCallException(get_class($this).'::'.$joint.' - join methods cannot take arguments');
		}

		if(in_array($joint, $this->joints))
		{
			return $this;
		}
		else
		{
			return $this->newScoped($scope)->addJoint($joint);
		}
	}


	/**
	 * Some helper methods to fetch results from the query
	 */

	protected function columnName($column)
	{
		if(strpos($column, '.') !== false)
			return $column;

		return $this->getTable().'.'.$column;
	}

	public function c($column = null)
	{
		if($column !== null)
		{
			return $this->columnName($column);
		}
		else
		{
			return function ($column) {
				return $this->columnName($column);
			};
		}
	}

	public function create(array $attributes)
	{
		$m = $this->newInstance();
		$m->fill($attributes)->save();
		return $m;
	}

	public function update(Model $m, array $attributes)
	{
		$m->fill($attributes)->save();
		return $m;
	}

	public function destroy(Model $m)
	{
		return $m->delete();
	}

	protected function grammar()
	{
		return $this->getConnection()->getQueryGrammar();
	}

	protected function raw($param)
	{
		return $this->getConnection()->raw($param);
	}

	protected function buildSelect()
	{
		$select = array($this->c('*'));

		foreach ($this->includedFields as $name => $field)
		{
			if ($this->grammar()->isExpression($field))
			{
				$select[] = $this->raw($field . ' as ' . $this->grammar()->wrap($name));
			}
			elseif ($name === $field)
			{
				$select[] = $this->c($field);
			}
			else
			{
				$select[] = $field . ' as ' . $name;
			}
		}

		return $select;
	}

	public function find()
	{
		$args = func_get_args();
		$q = $this->query();

		if(count($args) === 2)
		{
			list($field, $value) = $args;
		}
		else
		{
			$field = $this->getKeyName();
			$value = reset($args);
		}

		$field = $this->c($field);

		$q->select($this->buildSelect());

		return $q->where($field, '=', $value)->first();
	}

	public function first()
	{
		$q = $this->query();

		$q->select($this->buildSelect())->distinct();

		return $q->first();
	}

	public function get()
	{
		$q = $this->query();

		$q->select($this->buildSelect())->distinct();

		return $q->get();
	}

	public function paginate($perPage = 20)
	{
		$q = $this->query();

		$q->select($this->buildSelect())->distinct();

		return $q->paginate($perPage);
	}

	public function count()
	{
		$q = $this->query();

		$q->select($this->getQualifiedKeyname());

		return $q->count();
	}

	/**
	 * Some method to apply filters and orders to query
	 */
	public function applyFilters(Tools\Filterer $f)
	{
		return $f->run($this);
	}

	public function applySortings(Tools\Sorter $s)
	{
		return $s->run($this);
	}


	/**
	 * And something to get metadata and stuff from models, use responsibly
	 */
	public function __call($method, $args)
	{
		$m = $this->model->newInstance();
		
		if(method_exists($m, $method))
		{
			return call_user_func_array(array($m, $method), $args);
		}

		throw new BadMethodCallException(get_class($this).'::'.$method);
	}

}