<?php

namespace Fuel\Tasks;

class Base
{
	public function run()
	{
		
	}

	public function install()
	{
		\Config::load('../../../composer.json', 'composer', true, true);
		$repositories = \Config::get('composer.repositories', array());
		$repositories[] = array(
			'type' => 'vcs',
			'url'  => 'git@gitlab.firstcomputer.hu:fuel/pdf.git'
		);
		\Config::set('composer.repositories', $repositories);
		\Config::save('../../../composer.json', 'composer');
	}
}