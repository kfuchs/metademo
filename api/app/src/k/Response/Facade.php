<?php namespace k\Response;

use Illuminate;
use k\Reposed\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;
use Illuminate\Support\MessageBag;
use k\Reposed\Tools\Serializer;

class Facade extends Illuminate\Support\Facades\Response {

	protected static function serializer(Serializer $serializer = null)
	{
		return $serializer ? $serializer : new Serializer;
	}

	public static function paginator($paginator, array $meta = array(), Serializer $s = null)
	{
		return static::json([
			'total' => $paginator->getTotal(),
			'per_page' => $paginator->getPerPage(),
			'current_page' => $paginator->getCurrentPage(),
			'last_page' => $paginator->getLastPage(),
			'collection' => static::serializer($s)->run(Collection::make($paginator->getItems())),
			'meta' => $meta
		]);
	}

	public static function collection(Collection $collection, array $meta = array(), Serializer $s = null)
	{
		return static::json([
			'collection' => static::serializer($s)->run($collection),
			'meta' => $meta
		]);
	}

	public static function model(Model $m, array $meta = array(), Serializer $s = null)
	{
		return static::json([
			'resource' => static::serializer($s)->run($m),
			'meta' => $meta
		]);
	}

	public static function action(Model $m, $verb, array $meta = array(), Serializer $s = null)
	{
		return static::json([
			'msg' => 'Resource '.$verb,
			'id' => $m->id,
			'resource' => static::serializer($s)->run($m),
			'meta' => $meta
		]);
	}

	public static function errors($errors)
	{
		if($errors instanceof Validator) $errors = $errors->getMessageBag();
		if(is_array($errors)) $errors = new MessageBag($errors);

		return static::json([
				'msg' => 'Request Failed',
				'errors' => $errors->toArray()
			], 400);
	}

	public static function msg($msg, $status = 200)
	{
		return static::json([
			'msg' => $msg
		], $status);
	}

	public static function data(array $data, Serializer $s = null)
	{
		return static::json(static::serializer($s)->run($data));
	}

	public static function notFound()
	{
		return static::msg('Not Found', 404);
	}

	public static function notAllowed()
	{
		return static::msg('Not Allowed', 405);
	}

	public static function unauthorized()
	{
		return static::msg('Unauthorized', 401);
	}

	public static function forbidden()
	{
		return static::msg('Forbidden', 403);
	}

}