<?php

$admin_url = \Uri::admin(false);

return array(
	$admin_url.'login'                    => 'admin/login',
	$admin_url.'login/(.*?)'              => 'admin/login/$1',
	$admin_url.'logout'                   => 'admin/logout',
	$admin_url.'logout/(.*?)'             => 'admin/logout/$1',

	$admin_url.'themes(/.*)?'             => 'admin/themes$1',
	$admin_url.'enum/item/:enum_id(/.*)?' => 'admin/enum/item$2',
	$admin_url.'enum(/.*)?'               => 'admin/enum$1',
	$admin_url.'ajax(/.*)?'               => 'admin/ajax$1',

	$admin_url.'(.*?)/(.*?)'              => '$1/admin/$2',
	$admin_url.'(.*?)'                    => '$1/admin/index',
	rtrim($admin_url, '/')                => 'admin',
	'admin.*'                             => '404'
);