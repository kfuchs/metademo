<?php namespace Controller;

use Abstracts\Controller;
use Request;
use Response;
use Core\User;
use Input;
use Queue;

class Auth extends Controller {

	protected $transactionals = ['login', 'logout', 'register'];

	// actions

	public function login()
	{
		$req = Request::instance();

		if(\Auth::check($req))
		{
			return Response::msg('already logged in');
		}

		if(\Auth::authenticate($req))
		{
			return Response::action(\Auth::scope()->getUser(), 'logged in');
		}

		if(\Auth::hasOAuthCredentials($req) and \Auth::register($req))
		{
			$user = \Auth::scope()->getUser();

			return Response::action($user, 'registered');
		}

		return Response::msg('login failed', 400);
	}

	public function logout()
	{
		$req = Request::instance();

		\Auth::logout($req);

		return Response::msg('successfully logged out');
	}

	public function register()
	{
		$req = Request::instance();

		$val = User::repo()->newRegistrationValidator(Input::get());

		if($val->fails())
		{
			return Response::errors($val);
		}

		\Auth::register($req);

		return Response::action(\Auth::scope()->getUser(), 'registered');
	}

	public function showAccount()
	{
		return Response::model(\Auth::scope()->getUser());
	}

}