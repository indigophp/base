<?php

namespace Indigo\Base;

class Controller_Theme extends \Controller
{

	/**
	* @var string page template
	*/
	public $template = 'template';

	public $theme_type = 'frontend';

	/**
	 * Load the template and create the $this->theme object
	 */
	public function before($data = null)
	{
		if ($this->request->is_hmvc())
		{
			$this->theme = clone \Theme::instance('indigo');
		}
		else
		{
			$this->theme = \Theme::instance('indigo');
		}

		if ( ! $this->theme->find(\Config::get('base.theme.' . $this->theme_type)))
		{
			\Config::set('base.theme.' . $this->theme_type, 'default');
			\Config::save('base.db', 'base');
		}

		$this->theme->active(\Config::get('base.theme.' . $this->theme_type, 'default'));

		if ($engine = $this->theme->get_info('engine'))
		{
			$this->theme->set_config('view_ext', '.' . $engine);
		}
		if ( ! empty($this->template) and is_string($this->template))
		{
			// Load the template
			$this->template = $this->theme->set_template($this->template);
		}
		$this->template->set_global('asset', $this->theme->asset, false);

		return parent::before($data);
	}

	/**
	 * keep the after() as standard as possible to allow custom responses from actions
	 */
	public function after($response)
	{
		// If no response object was returned by the action,
		if (empty($response) or  ! $response instanceof Response)
		{
			// render the defined template
			$response = \Response::forge($this->theme->render());
		}

		return parent::after($response);
	}
}
