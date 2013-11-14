<?php

return array(
	'admin/login' => 'admin/login',
	'admin/login/(.*?)' => 'admin/login/$1',
	'admin/logout' => 'admin/logout',
	'admin/logout/(.*?)' => 'admin/logout/$1',

	'admin/themes(/.*)?' => 'admin/themes$1',
	'admin/enums(/.*)?' => 'admin/enums$1',
	'admin/ajax(/.*)?' => 'admin/ajax$1',

	'admin/(.*?)/(.*?)' => '$1/admin/$2',
	'admin/(.*?)' => '$1/admin/index'
);