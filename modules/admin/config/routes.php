<?php

return array(
	'admin/login' => 'admin/login',
	'admin/login/(.*?)' => 'admin/login/$1',
	'admin/logout' => 'admin/logout',
	'admin/logout/(.*?)' => 'admin/logout/$1',

	'admin/(.*?)/(.*?)' => '$1/admin/$2',
	'admin/(.*?)' => '$1/admin/index'
);