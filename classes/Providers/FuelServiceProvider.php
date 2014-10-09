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
	public $provides = true;

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

		// register Twig Environment
		$this->registerSingleton('twig', function($dic)
		{
			return \View_Twig::parser();
		});

		// inject menu extension in the twig
		$this->extend('twig', function($dic, $twig)
		{
			$extension = $dic->resolve('menu.twig.extension');

			$twig->addExtension($extension);
		});

		$this->provideMenu();
	}

	/**
	 * Provides admin menu items
	 */
	private function provideMenu()
	{
		$this->extendMultiton('menu', 'admin', function($dic, $menu)
		{
			// $menu = $dic->resolve('__parent__')->resolve('menu', [dgettext('indigoadmin', 'Indigo Admin')]);
			// $menu = $this->resolve('menu', [dgettext('indigoadmin', 'Indigo Admin')]);
			$menu->setName(dgettext('indigoadmin', 'Indigo Admin'));

			$menu->addChild('dashboard', [
				'label' => dgettext('indigoadmin', 'Dashboard'),
				'uri'   => \Uri::admin(false),
				'extras' => [
					'icon' => 'glyphicon glyphicon-dashboard',
				],
				'sort'  => 1,
			]);

			$authItem = $menu->addChild('authentication', [
				'label' => dgettext('indigoadmin', 'Authentication'),
				'extras' => [
					'icon' => 'glyphicon glyphicon-user',
				],
				'sort'  => 10,
			]);

			$authItem->addChild('users', [
				'label' => dgettext('indigoadmin', 'Users'),
				'uri'   => \Uri::admin(false).'auth',
			]);

			$authItem->addChild('permissions', [
				'label' => dgettext('indigoadmin', 'Permissions'),
				'uri'   => \Uri::admin(false).'auth/permissions',
			]);

			$settingsItem = $menu->addChild('settings', [
				'label' => dgettext('indigoadmin', 'Settings'),
				'extras' => [
					'icon' => 'fa fa-cogs',
				],
				'sort' => 100,
			]);

			$settingsItem->addChild('themes', [
				'label' => dgettext('indigoadmin', 'Themes'),
				'uri'   => \Uri::admin(false).'themes',
			]);

			$settingsItem->addChild('enums', [
				'label' => dgettext('indigoadmin', 'Enums'),
				'uri' => \Uri::admin(false).'enum',
			]);

			return $menu;
		});

		// $container = $this->multiton('container', 'menu.renderer');

		// $container->registerSingleton('admin', function($dic)
		// {
		// 	return $dic->resolve('twig');
		// });
	}
}
