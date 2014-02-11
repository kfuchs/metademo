<?php

/*
|--------------------------------------------------------------------------
| Query logging event
|--------------------------------------------------------------------------
*/

Event::listen('illuminate.query', function($query, $bindings, $time) {

	static $count;

	$logFile = __DIR__.'/storage/logs/queries';

	ob_start();
	print_r([$bindings, $query]);
	$str = ob_get_clean();

	if($count === null)
	{
		File::put($logFile, '');
		$count = 1;
	}
	
	$msg = $count++ . '---------------------------------------'.PHP_EOL;
	$msg .= $str.PHP_EOL;
	$msg .= '--------------------------------------------------------------'.PHP_EOL.PHP_EOL;
	
	File::append($logFile, $msg);


});
