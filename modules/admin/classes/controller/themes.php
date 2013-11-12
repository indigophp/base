<?php

namespace Admin;

class Controller_Themes extends Controller_Admin
{

	public function action_index()
	{
		$this->template->content = $this->theme->view('admin/themes/index');
		$this->template->content->admin_themes    = $this->themes('admin');
		$this->template->content->frontend_themes = $this->themes('frontend');
	}

	public function action_activate($type = 'frontend', $name = null)
	{
		if (is_null($name) or is_null($type) or ! in_array($type, array('frontend', 'admin')))
		{
			throw new \HttpNotFoundException();
		}

		if ( ! $this->theme->find($name))
		{
			throw new \HttpNotFoundException();
		}

		if ( ! \Auth::has_access('themes.change'))
		{
			\Session::set_flash('error', gettext('You have no permission to change the theme of this site.'));
			\Response::redirect_back('admin');
		}

		\Config::set('base.theme.' . $type, $name);
		\Config::save('base.db', 'base');

		\Session::set_flash('success', gettext('Theme successfully changed.'));
		\Response::redirect_back('admin/themes');
	}

	public function themes($type = null)
	{
		$themes = array_unique($this->theme->all());

		return array_map(
			function ($theme_name) use ($type)
			{
				$info = $this->theme->load_info($theme_name);
				$info['id'] = $theme_name;
				$info['is_active'] = $theme_name == \Config::get('base.theme.' . $type);
				return in_array($type, \Arr::get($info, 'supports', array())) ? $info : null;
			},
		$themes);

		return array_unique($this->theme->all());

		$return = array();

		foreach (\Package::loaded() as $package => $path)
		{
			$themesDir = $path . DS . 'themes';
			if ( ! is_dir($themesDir))
			{
				continue;
			}

			foreach (new \DirectoryIterator($themesDir) as $fileInfo)
			{
				if ( ! $this->is_valid_theme($fileInfo))
				{
					continue;
				}
				$return[$fileInfo->getFilename()] = $fileInfo->getFilename();
			}
		}

		return $return;

	}

	public function is_valid_theme($fileInfo)
	{
		return ! $fileInfo->isDot();
		foreach (new \DirectoryIterator($fileInfo) as $fileInfo)
		{

		}
	}

}