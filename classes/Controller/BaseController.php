<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Controller;

/**
 * Base Controller
 *
 * @author Tamás Barta <barta.tamas.d@gmail.com>
 */
class BaseController extends \Controller_Theme
{
	public function before($data = null)
	{
		parent::before($data);

		// Making the site name available in all views.
		$this->template->set_global('site_name', \Config::get('indigo.site_name', 'Indigo Admin'));

		// Make logged in user available in all views.
		$this->current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
		$this->template->set_global('current_user', $this->current_user, false);

		if ('twig' == $this->theme->get_info('engine'))
		{
			$paths = array();

			$theme_name = \Arr::get($this->theme->active(), 'name');

			$use_modules = $this->theme->get_config('use_modules');
			$path_prefix = null;
			$module_path = null;

			if ($use_modules and $request = \Request::active() and $module = $request->module)
			{
				// we're using module name prefixing
				$path_prefix = $module.DS;

				// and modules are in a separate path
				is_string($use_modules) and $path_prefix = trim($use_modules, '\\/').DS.$path_prefix;

				// do we need to check the module too?
				$use_modules === true and $module_path = \Module::exists($module).'themes'.DS;
			}

			foreach ($this->theme->get_parent_themes($theme_name) as $theme)
			{
				if ($use_modules and $module)
				{
					$paths[] = $theme['path'] . $path_prefix;
					$paths[] = $module_path.$theme['name'].DS;
				}

				foreach ($this->theme->get_paths() as $path)
				{
					$paths[] = $path . $theme['name'];
				}
			}

			$paths = array_filter($paths, 'is_dir');

			\Config::set('parser.View_Twig.views_paths', $paths);
		}
	}
}
