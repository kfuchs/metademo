<?php

return [

	'root' => [
		'local' => app('path.public').'/uploads',
		'awss3' => ''
	],

	'url-base' => [
		'local' => app('url')->to('uploads'),
		'awss3' => ''
	]

];