<?php

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

\Config::load('theme', true, true);
\Config::load('configpatch');

// This has to be loaded in order to have temporal working
\Config::load('orm', true);
// TODO: ez szar. nem megy tőle a migration, ha nincs még db. sőt, semmi más sem :D
\Config::load('base.db', true);

if ( ! \Fuel::$is_cli)
{
	if (\Config::get('admin_url') === null)
	{
		\Config::set('admin_url', 'admin/');
	}
}

Autoloader::add_core_namespace('Indigo\\Base');

Autoloader::add_classes(array(
	// Core extensions
	'Module'                => __DIR__ . '/classes/module.php',
	'Uri'                   => __DIR__ . '/classes/uri.php',
	'Twig_Indigo_Extension' => __DIR__ . '/classes/twig/indigo/extension.php',

	// Controllers
	'Indigo\\Base\\Controller_Assets'      => __DIR__ . '/classes/controller/assets.php',
	'Indigo\\Base\\Controller_Base'        => __DIR__ . '/classes/controller/base.php',
	'Indigo\\Base\\Controller_Theme'       => __DIR__ . '/classes/controller/theme.php',
	'Indigo\\Base\\Controller_Translation' => __DIR__ . '/classes/controller/translation.php',
	'Controller_Welcome'                   => __DIR__ . '/classes/controller/welcome.php',

	// HTTP Exceptions
	'Indigo\\Base\\HttpForbiddenException'       => __DIR__ . '/classes/httpexceptions.php',
	'Indigo\\Base\\HttpUnauthorizedException'    => __DIR__ . '/classes/httpexceptions.php',

	// Enum models
	'Indigo\\Base\\Model_Enum'      => __DIR__ . '/classes/model/enum.php',
	'Indigo\\Base\\Model_Enum_Item' => __DIR__ . '/classes/model/enum/item.php',
	'Indigo\\Base\\Model_Enum_Meta' => __DIR__ . '/classes/model/enum/meta.php',

	'Indigo\\Base\\Model_Tracker_Modifier'      => __DIR__ . '/classes/model/tracker/modifier.php',

	// Menu
	'Indigo\\Base\\Menu_Admin' => __DIR__ . '/classes/menu/admin.php',
));

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

\Module::load('admin');
\Module::load('auth');

\Package::load('menu');

// Disgusting hack. If I don't apply the locale here, the menu part of the translation does
// not work, because the locale is "C".
//
// TODO: do further investigation about how Fuel applies the locale.
// setlocale(LC_ALL, Config::get('locale'));

$menu = \Menu_Admin::instance('indigo');
$menu->add(array(
	array(
		'name' => gettext('Dashboard'),
		'url' => \Uri::admin(false),
		'icon' => 'glyphicon glyphicon-dashboard',
		'sort' => 1,
	),
	array(
		'name' => gettext('Authentication'),
		'icon' => 'glyphicon glyphicon-user',
		'sort' => 10,
		'children' => array(
			array(
				'name' => gettext('Users'),
				'url' => \Uri::admin(false).'auth',
			),
			array(
				'name' => gettext('Permissions'),
				'url' => \Uri::admin(false).'auth/permissions',
			),
		)
	),
	array(
		'name' => gettext('Settings'),
		'icon' => 'fa fa-cogs',
		'sort' => 100,
		'children' => array(
			array(
				'name' => gettext('Themes'),
				'url' => \Uri::admin(false).'themes',
			),
			array(
				'name' => gettext('Enums'),
				'url' => \Uri::admin(false).'enum',
			),
		)
	)
));


$routes = \Config::load('indigoroutes');
\Router::add($routes);