<?php namespace Controller\Api;

use Abstracts\ResourceController;
use Core\Widget;
use Core\Widget\Tag;
use Response;
use Uploader;
use Input;
use Request;

class Widgets extends ResourceController {

	protected function repo()
	{
		return Widget::repo();
	}

	protected function listingProvider()
	{
		return $this->repo()->forApiListing();
	}

	protected function resourceProvider()
	{
		return $this->repo()->forApiResource();
	}

	////////
	
	public function create()
	{
		// uploads processing
		$val = Uploader::newValidator(Request::instance());
		if($val->fails())
		{
			return Response::errors($val);
		}

		$resource = Uploader::process(Request::instance());
		Input::merge(['photo' => $resource->getUrl()]);

		// media items creation
		$val = $this->repo()->newValidator(Input::get());
		if($val->fails())
		{
			return Response::errors($val);
		}

		$m = $this->repo()->newPersistor(Input::get())->run();

		return Response::action($this->resourceProvider()->find($m->id), 'created');	
	}

	public function update()
	{
		$args = func_get_args(); $id = end($args);

		if(! $m = $this->resourceProvider()->find($id))
		{
			return Response::notFound();
		}

		$val = Uploader::newValidator(Request::instance());
		if($val->passes())
		{
			$resource = Uploader::process(Request::instance());
			Input::merge(['photo' => $resource->getUrl()]);
		}

		$val = $m->newValidator(Input::get());

		if($val->fails())
		{
			return Response::errors($val);
		}

		$m->newPersistor(Input::get())->run();

		return Response::action($this->resourceProvider()->find($m->id), 'updated');
	}

	////////
	
	public function attachTag($widgetId, $tagId)
	{
		$widget = Widget::repo()->find($widgetId);
		$tag = Tag::repo()->find($tagId);

		if(! $widget or ! $tag or ! $widget->canAttachTag($tag))
		{
			return Response::notFound();
		}

		$widget->attachTag($tag);

		return Response::action('attached', $tag);
	}

	public function detachTag($widgetId, $tagId)
	{
		$widget = Widget::repo()->find($widgetId);
		$tag = Tag::repo()->find($tagId);

		if(! $widget or ! $tag or ! $widget->hasTag($tag))
		{
			return Response::notFound();
		}

		$widget->detachTag($tag);

		return Response::action('detached', $tag);
	}

}