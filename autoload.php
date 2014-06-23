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

// This instance makes sure that namespace is resolved after all classes has been aliased
use Indigo\Core\Alias;

$manager = Alias::instance('namespace');
$manager->aliasNamespace('Indigo\\Base', '');
$manager->aliasNamespace('Indigo\\Base\\Exception', '');
// $manager->cache(__DIR__.'/autoload_cache.php', 'unwind');

$manager = Alias::instance('default');
// $manager->cache(__DIR__.'/autoload_cache.php', 'unwind');

$manager->alias(array(
	// Enum models
	'Indigo\\Base\\Model_Enum'      => 'Indigo\\Base\\Model\\EnumModel',
	'Indigo\\Base\\Model_Enum_Item' => 'Indigo\\Base\\Model\\Enum\\ItemModel',
	'Indigo\\Base\\Model_Enum_Meta' => 'Indigo\\Base\\Model\\Enum\\MetaModel',

	// Controllers
	'Indigo\\Base\\Controller_Assets'      => 'Indigo\\Base\\Controller\\AssetsController',
	'Indigo\\Base\\Controller_Base'        => 'Indigo\\Base\\Controller\\BaseController',
	'Indigo\\Base\\Controller_Theme'       => 'Indigo\\Base\\Controller\\ThemeController',
	'Indigo\\Base\\Controller_Translation' => 'Indigo\\Base\\Controller\\TranslationController',

	'Model\\Auth_User' => 'Indigo\\Base\\Model\\Auth\\UserModel',
));
