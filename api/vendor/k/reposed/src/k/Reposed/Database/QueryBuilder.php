<?php namespace k\Reposed\Database;

use Illuminate\Database\Query;

class QueryBuilder extends Query\Builder {

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'like', 'not like', 'between', 'ilike',
		'regexp'
	);

	/**
	 * Get a new instance of the query builder.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function newQuery()
	{
		return new static($this->connection, $this->grammar, $this->processor);
	}

	/**
	 * get aggregates over queries that have groups
	 *
	 * @param  string  $function
	 * @param  array   $columns
	 * @return mixed
	 */
	protected function groupedAggregate($function, $columns = array('*'))
	{
		if(is_null($this->columns)) $this->columns = $columns;

		$sql = "SELECT {$function}({$this->grammar->columnize($columns)}) AS aggregate FROM ({$this->toSql()}) AS results";
		return $this->connection->select($sql, $this->bindings);
	}

	/**
	 * Execute an aggregate over queries without groups
	 *
	 * @param  string  $function
	 * @param  array   $columns
	 * @return mixed
	 */
	public function ungroupedAggregate($function, $columns = array('*'))
	{
		$this->aggregate = compact('function', 'columns');
		return $this->get($columns);
	}

	/**
	 * Execute an aggregate function on the database.
	 *
	 * @param  string  $function
	 * @param  array   $columns
	 * @return mixed
	 */
	public function aggregate($function, $columns = array('*'))
	{
		if(isset($this->groups))
		{
			$results = $this->groupedAggregate($function, $columns);
		}
		else
		{
			$results = $this->ungroupedAggregate($function, $columns);
		}

		// Once we have executed the query, we will reset the aggregate property so
		// that more select queries can be executed against the database without
		// the aggregate value getting in the way when the grammar builds it.
		$this->columns = null; $this->aggregate = null;

		if (isset($results[0]))
		{
			$result = (array) $results[0];

			return $result['aggregate'];
		}
	}

	/**
	 * Get a paginator for the "select" statement.
	 *
	 * @param  int    $perPage
	 * @param  array  $columns
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function paginate($perPage = 15, $columns = array('*'))
	{
		$paginator = $this->connection->getPaginator();

		$total = $this->getPaginationCount();

		// Once we have the total number of records to be paginated, we can grab the
		// current page and the result array. Then we are ready to create a brand
		// new Paginator instances for the results which will create the links.
		$page = $paginator->getCurrentPage();

		$results = $this->forPage($page, $perPage)->get($columns);

		return $paginator->make($results, $total, $perPage);
	}

	protected function groupedPaginate($paginator, $perPage, $columns)
	{
		throw new \BadMethodCallException(get_class($this).'::'.__FUNCTION__);
	}

	public function buildRawPaginator($paginator, $results, $perPage)
	{
		throw new \BadMethodCallException(get_class($this).'::'.__FUNCTION__);
	}

	protected function ungroupedPaginate($paginator, $perPage, $columns)
	{
		throw new \BadMethodCallException(get_class($this).'::'.__FUNCTION__);
	}

	/**
	 * Get the count of the total records for pagination.
	 *
	 * @return int
	 */
	public function getPaginationCount()
	{
		list($orders, $this->orders) = array($this->orders, null);

		$columns = $this->columns;

		// Because some database engines may throw errors if we leave the ordering
		// statements on the query, we will "back them up" and remove them from
		// the query. Once we have the count we will put them back onto this.
		$total = $this->count();

		$this->orders = $orders;

		// Once the query is run we need to put the old select columns back on the
		// instance so that the select query will run properly. Otherwise, they
		// will be cleared, then the query will fire with all of the columns.
		$this->columns = $columns;

		return $total;
	}

}