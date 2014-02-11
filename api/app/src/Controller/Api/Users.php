<?php namespace Controller\Api;

use Abstracts\ResourceController;
use Core\User;
use Core\User\Role;
use Response;

class Users extends ResourceController {

	protected function repo()
	{
		return User::repo()->otherThanUser(\Auth::scope()->getUser());
	}

	protected function listingProvider()
	{
		return $this->repo()->forApiListing();
	}

	protected function resourceProvider()
	{
		return $this->repo()->forApiResource();
	}

	//////
	
	public function attachRole($userId, $roleId)
	{
		$user = User::repo()->find($userId);
		$role = Role::repo()->find($roleId);

		if(! $user or ! $role or ! $user->canAttachRole($role))
		{
			return Response::notFound();
		}

		$user->attachRole($role);

		return Response::action('attached', $role);
	}

	public function detachRole($userId, $roleId)
	{
		$user = User::repo()->find($userId);
		$role = Role::repo()->find($roleId);

		if(! $user or ! $role or ! $user->hasRole($role))
		{
			return Response::notFound();
		}

		$user->detachRole($role);

		return Response::action('detached', $role);
	}

}