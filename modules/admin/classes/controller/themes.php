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

	public function themes($type = null)
	{

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