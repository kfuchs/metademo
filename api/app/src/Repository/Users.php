<?php namespace Repository;

use Abstracts;
use k\Auth\Contracts\UserRepositoryInterface as AuthUserRepositoryInterface;
use k\Auth\Contracts\Standard;
use k\Auth\Contracts\Facebook;
use Core\User;
use Core\User\OAuthAccount;
use DB;
use k\Reposed\Tools;

class Users extends Abstracts\Repository implements AuthUserRepositoryInterface, Standard\UserRepositoryInterface, Facebook\UserRepositoryInterface {
	
	protected function joinOAuthAccounts()
	{
		return $this->newJoint(function ($q) {
			$q->join(
				OAuthAccount::table(),
				OAuthAccount::c('user_id'), '=', $this->c('id')
			);
		});
	}

	protected function scopeFacebookAccounts()
	{
		return $this->joinOAuthAccounts()->newScoped(function ($q) {
			$q->where(OAuthAccount::c('provider'), '=', OAuthAccount::facebookType);
		});
	}

	protected function withRoles()
	{
		return $this->newScoped(function ($q) {
			$q->with('roles');
		});
	}

	protected function joinRoles()
	{
		return $this->newJoint(function ($q) {
			$q->join(
				$this->roles()->getTable(),
				$this->roles()->getTable().'.user_id', '=', $this->c('id')
			  )
			  ->join(
			  	User\Role::table(),
			  	User\Role::c('id'), '=', $this->roles()->getTable().'.role_id'
			  );
		});
	}

	protected function leftJoinRoles()
	{
		return $this->newJoint(function ($q) {
			$q->leftJoin(
				$this->roles()->getTable(),
				$this->roles()->getTable().'.user_id', '=', $this->c('id')
			  )
			  ->leftJoin(
			  	User\Role::table(),
			  	User\Role::c('id'), '=', $this->roles()->getTable().'.role_id'
			  );
		});	
	}

	protected function withPushServersData()
	{
		return $this->newJoint(function ($q) {
			$q->with('pushServers');
		});
	}

	/**
	 * Auth\Contracts\UserRepositoryInterface methods
	 */
	public function findByAuthIdentifier($identifier)
	{
		return $this->find($identifier);
	}

	/**
	 * Auth\Contracts\Standard\UserRepositoryInterface methods
	 */
	public function findByUserCredentials($credentials)
	{
		// $credentials, acc to config is email
		return $this->find('email', $credentials);
	}

	public function createFromRegistrationData($data)
	{
		// this registration data is supposed to be verified and have 
		// auth email and password and repeated password
		return $this->newRegistrationPersistor($data)->run()->getModel();
	}

	/**
	 * Auth\Contracts\Facebook\UserRepositoryInterface methods
	 */
	public function findByFacebookData($data)
	{
		$fbId = isset($data['id']) ? $data['id'] : null;

		return $this->scopeFacebookAccounts()->find(OAuthAccount::c('account_id'), $fbId);
	}

	public function createFromFacebookData($data)
	{
		try
		{
			return $this->newFacebookPersistor($data)->run()->getModel();
		}
		catch(Exception $e)
		{
			return null;
		}
	}

	/**
	 * General public interface
	 */
	public function scopeAdmins()
	{
		return $this->joinRoles()->newScoped(function ($q) {
			$q->whereIn(User\Role::c('name'), User\Role::$adminRoles);
		});
	}

	public function scopeSuperadmins()
	{
		return $this->joinRoles()->newScoped(function ($q) {
			$q->whereIn(User\Role::c('name'), User\Role::$superAdminRoles);
		});
	}

	public function forIds(array $userIds)
	{
		return $this->newScoped(function ($q) use ($userIds) {
			if($userIds) $q->whereIn($this->c('id'), $userIds);
			else         $q->whereNull($this->c('id'));
		});
	}

	public function otherThanUser(User $user = null)
	{
		return $this->newScoped(function ($q) use ($user) {
			$q->where($this->c('id'), '<>', $user->id);
		});
	}

	/**
	 * Admin Api methods
	 */
	public function forApiListing()
	{
		return $this;
	}

	public function forApiResource()
	{
		return $this->withRoles();
	}

	/**
	* filterer
	*/
	public function newFilterer(array $params = array())
	{
		$f = (new Tools\Filterer)->setParams($params);

		$f->setFilterables([
			'first_name' => $this->c('first_name'),
			'last_name' => $this->c('last_name'),
			'username' => $this->c('username'),
			'email' => $this->c('email'),
		]);

		return $f;
	}

}
