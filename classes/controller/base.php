<?php

namespace Indigo\Base;

class Controller_Base extends \Controller_Theme
{

	public function before($data = null)
	{
		parent::before($data);

		// Making the site name available in all views.
		$this->template->set_global('site_name', \Config::get('app.site_name', 'Indigo Admin'));

		// Make logged in user available in all views.
		$this->current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
		$this->template->set_global('current_user', $this->current_user, false);

		if ('twig' == $this->theme->get_info('engine'))
		{

			$paths = array();

			$theme_name = \Arr::get($this->theme->active(), 'name');

			foreach ($this->theme->get_parent_themes($theme_name) as $theme)
			{
				foreach ($this->theme->get_paths() as $path)
				{
					if (is_dir($path .= $theme['name']))
					{
						$paths[] = $path ;
					}
				}
			}

			\Config::set('parser.View_Twig.views_paths', $paths);
		}
	}

}