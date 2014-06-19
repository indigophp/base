<?php

namespace Admin;

class Controller_Themes extends Controller_Base
{
	public function action_index()
	{
		$this->template->content = $this->theme->view('admin/themes/index');
		$this->template->content->admin_themes    = $this->themes('admin');
		$this->template->content->frontend_themes = $this->themes('frontend');
		$this->template->title = gettext('Themes');
	}

	public function action_activate($name, $type = 'frontend')
	{
		if ( ! in_array($type, array('frontend', 'admin')) or ! $this->theme->find($name))
		{
			throw new \HttpNotFoundException();
		}

		if ( ! \Auth::has_access('themes.change'))
		{
			\Logger::instance('alert')->error(gettext('You have no permission to change the theme of this site.'));
			\Response::redirect_back('admin');
		}

		\Config::set('base.theme.' . $type, $name);
		\Config::save('base', 'base');

		$context = array(
			'template' => 'success'
		);

		\Logger::instance('alert')->info(gettext('Theme successfully changed.'), $context);
		\Response::redirect_back('admin/themes');
	}

	public function action_preview($name = null)
	{
		try
		{
			$image = \File::read($this->theme->find($name) . DS . 'preview.png', true);
		}
		catch (\InvalidPathException $e)
		{
			$image = \File::read(BASEPATH.'themes'.DS.'default'.DS.'default.png', true);
		}

		return \Response::forge($image, 200, array('Content-type' => 'image/png'));
	}

	public function themes($type = null)
	{
		$themes = array_unique($this->theme->all());

		return array_filter(array_map(
			function ($theme_name) use ($type)
			{
				$info = $this->theme->load_info($theme_name);
				$info['id'] = $theme_name;
				$info['is_active'] = $theme_name == \Config::get('base.theme.' . $type);
				return in_array($type, \Arr::get($info, 'supports', array())) ? $info : null;
			},
		$themes));
	}

}