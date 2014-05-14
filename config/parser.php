<?php

return array(
	'View_Twig' => array(
		'extensions' => array(
			'Twig\\Extension\\Indigo',
			'Twig_Extensions_Extension_I18n',
		),
		'environment' => array(
			'autoescape' => false,
		),
	),
);