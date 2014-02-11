<?php namespace Service\Uploader;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\AwsS3;


class UrlGenerator {

	protected $fs;
	protected $config;

	public function __construct(Filesystem $fs, array $config)
	{
		$this->fs = $fs;
		$this->config = $config;
	}

	public function to($name)
	{
		$adapter = $this->fs->getAdapter();
		
		switch ($adapter) {
			case $adapter instanceof Local: return $this->config['local'].'/'.$name;
			case $adapter instanceof AwsS3: return $this->config['awss3'].'/'.$name;
			default : throw new \RuntimeException(static::class.' : unidentified adapter - ' . get_class($adapter));
		}
	}

}