<?php namespace Repository\Widget;

use Abstracts;
use Core\Widget;

class Tags extends Abstracts\Repository {

	protected function joinWidgetsPivot()
	{
		return $this->newJoint(function ($q) {
			$pivot = $this->widgets()->getTable();
			$q->join($pivot, $pivot.'.tag_id', '=', $this->c('id'));
		});
	}

	protected function leftJoinWidgetsPivot()
	{
		return $this->newJoint(function ($q) {
			$pivot = $this->widgets()->getTable();
			$q->leftJoin($pivot, $pivot.'.tag_id', '=', $this->c('id'));
		});	
	}

	protected function joinWidgets()
	{
		return $this->joinWidgets()->newJoint(function ($q) {
			$pivot = $this->widgets()->getTable();
			$q->join(Widget::table(), $pivot.'.widget_id', '=', Widget::c('id'));
		});
	}

	protected function leftJoinWidgets()
	{
		return $this->leftJoinWidgetsPivot()->newJoint(function ($q) {
			$pivot = $this->widgets()->getTable();
			$q->leftJoin(Widget::table(), $pivot.'.widget_id', '=', Widget::c('id'));
		});
	}

	/////////
	
	public function forApiListing()
	{
		return $this;
	}

	public function forApiResource()
	{
		return $this;
	}

}