<?php

/*
 * This file is part of the Indigo Base component.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * NOTICE:
 *
 * This is the route configuration for this FuelPHP application.
 * It contains configuration which is for this application only.
 */

// 404 route
$this->router->all(null, 'welcome/404', '404');

// homepage route
$this->router->all('/', 'welcome/index', 'root');

$this->router->get('themes/{segment}/{any}')
	->filters([
		'controller' => 'Indigo\Common\Controller\Assets',
		'action' => 'index',
	]);
