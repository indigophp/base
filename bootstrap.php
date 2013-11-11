<?php

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

$module_paths = \Config::get( 'module_paths', array() );
$module_paths[] = BASEPATH.'modules'.DS;
\Config::set('module_paths', $module_paths);

// Adding the possible theme paths to the config of Theme.
$theme_paths = \Config::get('theme.paths', array());
$theme_paths[] = BASEPATH.'themes';
$theme_paths[] = BASEPATH.'modules'.DS.'auth'.DS.'themes';
$theme_paths[] = BASEPATH.'modules'.DS.'admin'.DS.'themes';
\Config::set('theme.paths', $theme_paths);

Autoloader::add_core_namespace('Indigo\\Base');

Autoloader::add_classes(array(
	'Module'                       => __DIR__ . '/classes/module.php',
	'Controller_Translation'       => __DIR__ . '/classes/controller/translation.php',
	'Controller_Base'              => __DIR__ . '/classes/controller/base.php',
	'Controller_Assets'            => __DIR__ . '/classes/controller/assets.php',
	'Controller_Welcome'           => __DIR__ . '/classes/controller/welcome.php',
	'Fuel\\Core\\Controller_Theme' => __DIR__ . '/classes/controller/theme.php',
));

Autoloader::add_classes(array(
	'Indigo\\Base\\Model_Enum'      => __DIR__ . '/classes/model/enum.php',
	'Indigo\\Base\\Model_Enum_Meta' => __DIR__ . '/classes/model/enum/meta.php',
));

\Config::load('theme', true, true);
\Config::load('base');


\Module::load('admin');
\Module::load('auth');