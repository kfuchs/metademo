<?php namespace k\Reposed;

use Illuminate\Database\Eloquent;

class Builder extends Eloquent\Builder {

	/**
	 * Get a paginator for the "select" statement.
	 *
	 * @param  int    $perPage
	 * @param  array  $columns
	 * @return \Illuminate\Pagination\Paginator
	 */
	public function paginate($perPage = null, $columns = array('*'))
	{
		$perPage = $perPage ?: $this->model->getPerPage();

		$paginator = $this->query->getConnection()->getPaginator();

		$total = $this->query->getPaginationCount();

		// Once we have the paginator we need to set the limit and offset values for
		// the query so we can get the properly paginated items. Once we have an
		// array of items we can create the paginator instances for the items.
		$page = $paginator->getCurrentPage();

		$this->query->forPage($page, $perPage);

		return $paginator->make($this->get($columns)->all(), $total, $perPage);
	}

	protected function groupedPaginate($paginator, $perPage, $columns)
	{
		throw new \BadMethodCallException(get_class($this).'::'.__FUNCTION__);
	}

	protected function ungroupedPaginate($paginator, $perPage, $columns)
	{
		throw new \BadMethodCallException(get_class($this).'::'.__FUNCTION__);
	}

}