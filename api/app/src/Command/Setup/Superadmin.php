<?php namespace Command\Setup;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Core\User;

class Superadmin extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'setup:superadmin';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Used to setup superadmin';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if(! $role = User\Role::repo()->find('name', 'superadmin'))
		{
			$role = User\Role::repo()->create(['name' => 'superadmin']);
		}

		if(! $user = User::repo()->find('username', 'superadmin'))
		{
			$user = User::repo()->create([
				'username' => 'superadmin',
				'email'   => 'superadmin@metademo.com',
				'password' => 'superadmin@)!$',
				'first_name' => 'Super',
				'last_name' => 'Admin'
			]);
		}

		if(! $user->hasRole($role))
		{
			$user->attachRole($role);
		}
	}

}