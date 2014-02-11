<?php namespace Command\Db;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB;
use Artisan;
use DatabaseSeeder;
use App;
use Command\Setup;

class Reseed extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'db:reseed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Used to reseed tables with data.';

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
		(new Clear)->fire();
		(new DatabaseSeeder)->setContainer(app())->setCommand($this)->run();
	}

}