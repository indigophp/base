<?php

class Controller_Base extends \Controller_Theme
{

	public function before($data = null)
	{
		parent::before();

		// Making the site name available in all views.
		$this->template->set_global('site_name', 'Indigo Admin');

		// Make logged in user available in all views.
		$this->current_user = \Model\Auth_User::find_by_username(Auth::get_screen_name());
		View::set_global('current_user', $this->current_user);

		if ('twig' == $this->theme->get_info('engine')) {

			$theme_name = \Arr::get($this->theme->active(), 'name', 'default');

			// This array is going to be used as the parameter for the constructor of Twig_Loader_Filesystem,
			// so only files in these paths are searched for being loaded
			$paths = array();

			// Iterate through packages, and add their theme path
			foreach (Package::loaded() as $package => $path) {
				if (file_exists($path.'themes'.DS.$theme_name))
				{
					$paths[] = $path.'themes'.DS.$theme_name;
				}
			}

			// Iterate through modules, and add them namespaced (I hope it gets added namespaced)
			// The container directory of twigs that are referenced from View::forge() are automatically
			// added, so for those namespaces are not required to specify.
			//
			// Update: It doesn't get namespaced, but we can stick with it
			foreach (Module::loaded() as $module => $path) {
				if (file_exists($path.'themes'.DS.$theme_name))
				{
					$paths[$module] = $path.'themes'.DS.$theme_name;
				}
			}

			// The loader gets the parameter from the parser's config
			Config::set('parser.View_Twig.views_paths', $paths);
		}
	}

}