<?php

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

\Config::load('theme', true, true);
\Config::load('patch');
\Config::load('base', true);

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
	'Indigo\\Base\\Uri'            => __DIR__ . '/classes/uri.php',
	'Indigo\\Base\\Str'            => __DIR__ . '/classes/str.php',
	'Indigo\\Orm\\Observer_Typing' => __DIR__ . '/classes/observer/typing.php',

	// Menu
	'Indigo\\Base\\Menu_Admin' => __DIR__ . '/classes/menu/admin.php',
	'Fuel\\Migrations\\Migration_Enum' => __DIR__ . '/migrations/enum.php',
));

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

\Event::register('app_created', function() {
	$routes = \Config::load('indigoroutes');
	\Router::add($routes);
});
