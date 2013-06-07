<?php

$paths = \Config::get( 'module_paths', array() );
$paths[] = __DIR__.DS.'modules'.DS;
\Config::set('module_paths', $paths);

Autoloader::add_core_namespace('Base');

Autoloader::add_classes(array(
	// 'Admin\\Admin' => __DIR__.'/modules/admin/classes/admin.php',
	'Base\\Module' => __DIR__ . '/classes/module.php',
));

\Config::load('theme', true, true);

\Theme::instance();

// Autoloader::load('Admin\\Admin');

\Module::load('admin');
\Module::load('auth');
\Package::load('rss');