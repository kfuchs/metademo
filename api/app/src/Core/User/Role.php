<?php namespace Core\User;

use Abstracts;
use Core\User;
use k\Reposed\Tools;
use Validator;
use Repository;

class Role extends Abstracts\Model {

	/**
	 * Settings and Metadata
	 */
	protected $table = 'user_roles';

	public static $superAdminRoles = ['superadmin'];

	public static $adminRoles = ['superadmin', 'admin'];

	public function newRepository()
	{
		return new Repository\User\Roles($this);
	}

	/**
	 * Relations
	 */
	public function users()
	{
		return $this->belongsTo(User::class, 'user_user_role', 'role_id', 'user_id');
	}

	/**
	* Validator
	*/
	public function newValidator(array $data = array())
	{
		$rules = [
			'name' => 'required|unique:'.static::table().',name,'.(int)$this->id
		];

		$val = Validator::make($data, $rules);

		return $val;
	}

	/**
	* Persistor
	*/
	public function newPersistor(array $data = array())
	{
		$p = (new Tools\Persistor($this))->setData($data);
		$p->setFields(['name']);

		return $p;
	}

}
