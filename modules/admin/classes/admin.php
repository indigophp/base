<?php

namespace Admin;

class Admin
{
	public static function _init()
	{
		$monolog = new \Monolog\Logger('firephp');
		$stream = new \Monolog\Handler\FirePHPHandler();
		$monolog->pushHandler($stream);

		$monolog->log('WARNING', 'This is a test message');
	}
}