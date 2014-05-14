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
 * Theme Controller
 *
 * @author Tamás Barta <barta.tamas.d@gmail.com>
 */
class ThemeController extends \Controller
{
	/**
	 * Page template
	 *
	 * @var string
	 */
	public $template = 'template';

	/**
	 * Theme type (eg. frontend or backend)
	 *
	 * @var string
	 */
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
			\Config::save('base', 'base');
		}

		$this->theme->active(\Config::get('base.theme.' . $this->theme_type, 'default'));

		if ($engine = $this->theme->get_info('engine'))
		{
			$this->theme->set_config('view_ext', '.' . $engine);
		}

		if ( ! empty($this->template) and is_string($this->template))
		{
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
		if (empty($response) or  ! $response instanceof \Response)
		{
			// render the defined template
			$response = \Response::forge($this->theme->render());
		}

		return parent::after($response);
	}
}
