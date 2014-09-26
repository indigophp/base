<?php

use Indigo\Fuel\Dependency\Container as DiC;
use Fuel\Dependency\ResolveException;

define('BASEPATH', __DIR__.DIRECTORY_SEPARATOR);

Autoloader::add_core_namespace('Indigo\\Base');

Autoloader::add_classes(array(
	// Core extensions
	'Indigo\\Base\\Uri'            => __DIR__ . '/classes/uri.php',
	'Indigo\\Orm\\Observer_Typing' => __DIR__ . '/classes/observer/typing.php',
));

// GNU Gettext translation settings
bindtextdomain('indigoadmin', APPPATH.'lang');
bind_textdomain_codeset('indigoadmin', 'UTF-8');

// Choose domain
textdomain('indigoadmin');

// ugly hack: twig must be loaded here to make sure menu template is loaded
DiC::resolve('twig');

// Disgusting hack. If I don't apply the locale here, the menu part of the translation does
// not work, because the locale is "C".
//
// TODO: do further investigation about how Fuel applies the locale.
// setlocale(LC_ALL, Config::get('locale'));

\Event::register('app_created', function() {
	$routes = \Config::load('indigoroutes');
	\Router::add($routes);
});
