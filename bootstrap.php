<?php

$paths = \Config::get( 'module_paths', array() );
$paths[] = __DIR__.DS.'modules'.DS;
\Config::set('module_paths', $paths);

\Module::load('admin');
\Module::load('auth');

// Autoloader::add_core_namespace('Admin');

// Autoloader::add_classes(array(
// 	'Admin\\Admin' => __DIR__.'/modules/admin/classes/admin.php',
// ));

Autoloader::load('Admin\\Admin');