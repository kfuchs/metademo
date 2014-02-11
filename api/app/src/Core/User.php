<?php namespace Core;

use Abstracts;
use Validator;
use k\Reposed\Tools;
use Hash;
use k\Auth\Contracts\UserInterface;
use k\Auth\Contracts\Standard;
use k\Auth\Contracts\Facebook;
use Repository;
use Core\User\OAuthAccount;
use Carbon\Carbon;
use DB;

class User extends Abstracts\Model implements UserInterface, Standard\UserInterface, Facebook\UserInterface {

	/**
	 * Settings and Metadata
	 */
	protected $table = 'users';

	protected $hidden = ['password'];

	public function newRepository()
	{
		return new Repository\Users($this);
	}
	
	/**
	 * Relations
	 */
	public function roles()
	{
		return $this->belongsToMany(User\Role::class, 'user_user_role', 'user_id', 'role_id');
	}

	public function oauthAccounts()
	{
		return $this->hasMany(User\OAuthAccount::class, 'user_id');
	}

	public function facebookOAuthAccount()
	{
		return $this->hasOne(User\OAuthAccount::class, 'user_id')
					->where('provider', '=', User\OAuthAccount::facebookType);
	}

	/***********************/

	/**
	 * k\Auth\Contracts\UserInterface methods
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * k\Auth\Contracts\Standard\UserInterface methods
	 */
	public function checkPassword($password)
	{
		return Hash::check($password, $this->password);	
	}

	/**
	 * k\Auth\Contracts\Facebook\UserInterface methods
	 */
	public function setFacebookToken($token)
	{
		if(! $this->facebookOAuthAccount)
		{
			throw new \BadMethodcallException(static::class.'::'.__FUNCTION__);
		}

		$this->facebookOAuthAccount->fill(['token' => $token])->save();
	}


	/**
	 * Setters getters and related methods
	 */
	public function setPasswordAttribute($password)
	{
		$this->attributes['password'] = Hash::make($password);
	}

	public function hasAdminRoles()
	{
		foreach($this->roles as $role)
		{
			if(in_array($role->name, User\Role::$adminRoles)) return true;
		}

		return false;
	}

	public function getIsAdminAttribute()
	{
		return $this->hasAdminRoles();
	}

	public function setFullNameAttribute($fullName)
	{
		$parts = explode(' ', $fullName);

		$last = end($parts);
		$first = implode(' ', array_slice($parts, 0, -1));
		
		$this->firstName = $first;
		$this->lastName = $last;	
	}

	/**
	 * Model Methods
	 */
	public function hasRole(User\Role $role)
	{
		return $this->roles()->where(User\Role::c('id'), '=', $role->id)->first() !== null;
	}

	public function attachRole(User\Role $role)
	{
		$this->roles()->attach($role);
	}

	/**
	 * Registration setup
	 */
	public function newRegistrationValidator(array $data = array())
	{
		$rules = [
			'email'    => 'required|email|unique:'.static::table(),
			'password' => 'required|confirmed'
		];

		return Validator::make($data, $rules);
	}

	protected function makeUsernameFromEmail($email)
	{
		list($username, $email) = explode('@', $email);
		return $username;
	}

	public function newRegistrationPersistor(array $data = array())
	{
		if($this->exists)
		{
			throw new \BadMethodcallException(static::class.'::'.__FUNCTION__);
		}

		$data['username'] = isset($data['username']) ? $data['username'] : $this->makeUsernameFromEmail($data['email']);

		$p = (new Tools\Persistor($this))->setData($data);
		$p->setFields(['email', 'password', 'username', 'first_name', 'last_name']);

		return $p;
	}

	protected function checkFieldUsability($str)
	{
		return strlen($str) === strlen(utf8_decode($str));
	}

	public function newFacebookPersistor(array $data = array())
	{
		if($this->exists)
		{
			throw new \BadMethodCallException(static::class.'::'.__FUNCTION__);
		}

		if(! isset($data['email'], $data['id']))
		{
			throw new \InvalidArgumentException(static::class.'::'.__FUNCTION__.':$data => '. json_encode($data));
		}

		$data['username'] = isset($data['username']) ? $data['username'] : $this->makeUsernameFromEmail($data['email']);
		$data['facebook'] = ['account_id' => $data['id']];

		if($this->checkFieldUsability($data['first_name']) && $this->checkFieldUsability($data['last_name']))
		{
			$data['first_name'] = $data['first_name'];
			$data['last_name'] = $data['last_name'];
		}

		$p = (new Tools\Persistor($this))->setData($data);
		$p->setFields(['username', 'first_name', 'last_name', 'email']);
		$p->setRelations(['facebook']);

		$p->addRelationProcessor('facebook', function ($m, $rel, $data) {
			$data['provider'] = OAuthAccount::facebookType;
			$data['user_id']  = $m->id;
			OAuthAccount::repo()->create($data);
		});

		return $p;
	}


}
