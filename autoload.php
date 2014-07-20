<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Aliased classes to work with Fuel v1
 */

use Indigo\Core\Alias;

$manager = Alias::instance('namespace');
$manager->aliasNamespace('Indigo\\Base', '');

$manager = Alias::instance('default');

$manager->alias(array(
	// Controllers
	'Indigo\\Base\\Controller_Assets'      => 'Indigo\\Base\\Controller\\AssetsController',
	'Indigo\\Base\\Controller_Base'        => 'Indigo\\Base\\Controller\\BaseController',
	'Indigo\\Base\\Controller_Theme'       => 'Indigo\\Base\\Controller\\ThemeController',
	'Indigo\\Base\\Controller_Translation' => 'Indigo\\Base\\Controller\\TranslationController',
));
