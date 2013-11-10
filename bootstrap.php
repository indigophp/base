<?php

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

define('NUMBER_OF_RIGHTS_IN_BASE', 1);

$paths = \Config::get( 'module_paths', array() );
$paths[] = BASEPATH.'modules'.DS;
\Config::set('module_paths', $paths);

Autoloader::add_core_namespace('Base');

Autoloader::add_classes(array(
	'Module'                       => __DIR__ . '/classes/module.php',
	'Controller_Translation'       => __DIR__ . '/classes/controller/translation.php',
	'Controller_Base'              => __DIR__ . '/classes/controller/base.php',
	'Controller_Assets'            => __DIR__ . '/classes/controller/assets.php',
	'Controller_Welcome'           => __DIR__ . '/classes/controller/welcome.php',
	'Fuel\\Core\\Controller_Theme' => __DIR__ . '/classes/controller/theme.php',
));

\Config::load('theme', true, true);
\Config::load('base');


\Module::load('admin');
\Module::load('auth');