<?php

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

\Config::load('theme', true, true);
\Config::load('base');
\Config::load('base.db', true);

$module_paths = \Config::get('module_paths', array());
$module_paths[] = BASEPATH.'modules'.DS;
\Config::set('module_paths', $module_paths);

// Adding the possible theme paths to the config of Theme.
$theme_paths = array();
$theme_paths[] = BASEPATH.'themes';
$theme_paths[] = BASEPATH.'modules'.DS.'auth'.DS.'themes';
$theme_paths[] = BASEPATH.'modules'.DS.'admin'.DS.'themes';
\Theme::instance('indigo')->add_paths($theme_paths);


// GNU Gettext translation settings
bindtextdomain('indigoadmin', APPPATH.'lang');
bind_textdomain_codeset('indigoadmin', 'UTF-8');

// Choose domain
textdomain('indigoadmin');

Autoloader::add_core_namespace('Indigo\\Base');

Autoloader::add_classes(array(
	'Module'                       => __DIR__ . '/classes/module.php',
	'Controller_Translation'       => __DIR__ . '/classes/controller/translation.php',
	'Controller_Base'              => __DIR__ . '/classes/controller/base.php',
	'Controller_Assets'            => __DIR__ . '/classes/controller/assets.php',
	'Controller_Welcome'           => __DIR__ . '/classes/controller/welcome.php',
	'Fuel\\Core\\Controller_Theme' => __DIR__ . '/classes/controller/theme.php',
	'Twig_Indigo_Extension'        => __DIR__ . '/classes/twig/indigo/extension.php',
));

Autoloader::add_classes(array(
	'Indigo\\Base\\Model_Enum'      => __DIR__ . '/classes/model/enum.php',
	'Indigo\\Base\\Model_Enum_Meta' => __DIR__ . '/classes/model/enum/meta.php',
	'Indigo\\Base\\Menu_Admin'      => __DIR__ . '/classes/menu/admin.php',
));

\Module::load('admin');
\Module::load('auth');

\Package::load('menu');

$menu = \Menu_Admin::instance('indigo');

$menu->add(array(
	array(
		'name' => 'Dashboard',
		'url' => 'admin',
		'icon' => 'glyphicon glyphicon-dashboard',
		'sort' => 1,
	),
	array(
		'name' => 'Authentication',
		'icon' => 'glyphicon glyphicon-user',
		'sort' => 10,
		'children' => array(
			array(
				'name' => 'Users',
				'url' => 'admin/auth',
			),
			array(
				'name' => 'Permissions',
				'url' => 'admin/auth/permissions',
			),
		)
	),
	array(
		'name' => 'Settings',
		'icon' => 'fa fa-cogs',
		'sort' => 100,
		'children' => array(
			array(
				'name' => 'Themes',
				'url' => 'admin/themes',
			),
		)
	)
));