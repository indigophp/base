<?php

namespace Indigo\Base;

class Menu_Admin extends \Menu
{
	public function render()
	{
		$menu = parent::render();

		array_walk($menu, function(&$item) {
			empty($item['sort']) and $item['sort'] = 99;
		});

		$menu = \Arr::sort($menu, 'sort');

		return $menu;
	}
}