<?php

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

$paths = \Config::get( 'module_paths', array() );
$paths[] = BASEPATH.'modules'.DS;
\Config::set('module_paths', $paths);

Autoloader::add_core_namespace('Base');

Autoloader::add_classes(array(
	'Module' => __DIR__ . '/classes/module.php',
	'Html' => __DIR__ . '/classes/html.php',
	'Fuel\\Core\\Controller_Theme' => __DIR__ . '/classes/controller/theme.php',
));

\Config::load('theme', true, true);
\Config::load('base');


// \Module::load('admin');
\Module::load('auth');