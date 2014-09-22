<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Providers;

use Fuel\Dependency\ServiceProvider;

/**
 * Provides Fuel Base services
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FuelServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 */
	public $provides = [
		'logger',
		'logger.alert',
	];

	/**
	 * {@inheritdoc}
	 */
	public function provide()
	{
		$this->registerSingleton('logger.alert', function($dic, $instance = 'default')
		{
			$logger = new \Monolog\Logger($instance);
			$handler = new \Monolog\Handler\AlertHandler;
			$logger->pushHandler($handler);

			return $logger;
		});
	}
}
