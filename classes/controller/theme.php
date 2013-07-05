<?php

namespace Fuel\Core;

class Controller_Theme extends Controller
{

	/**
	* @var string page template
	*/
	public $template = 'template';

	/**
	 * Load the template and create the $this->theme object
	 */
	public function before($data = null)
	{
		$this->theme = \Theme::instance();

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
