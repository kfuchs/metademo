<?php namespace Command\Db;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB;
use Config;

class Clear extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'db:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Used to clear all existing tables';

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
		$tables = [];
		
		$q = DB::getPdo()->prepare('show tables');
		$q->execute();

		while($row = $q->fetch())
		{
			if($row[0] !== Config::get('database.migrations'))
			{
				DB::getPdo()->prepare('truncate table '.$row[0])->execute();
			}
		}
	}

}