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
	use \Indigo\Core\Controller\ThemeController {
		init as theme_init;
	}

	/**
	 * Page template
	 *
	 * @var string
	 */
	public $template = 'template';

	/**
	 * Theme type (eg. frontend or admin)
	 *
	 * @var string
	 */
	public $theme_type = 'frontend';

	/**
	 * Theme instance
	 *
	 * @var string
	 */
	public $theme = 'indigo';

	/**
	 * {@inheritdocs}
	 */
	public function before()
	{
		// Clones theme instance in case of HMVC request
		if ($this->request->is_hmvc())
		{
			$this->theme = clone \Theme::instance($this->theme);
		}
		else
		{
			$this->theme = \Theme::instance('indigo');
		}

		// Returns active theme based on theme type
		$theme = \Config::get('indigo.theme.' . $this->theme_type);

		// Sets the fallback theme if no match found
		if ($this->theme->find($theme) === false)
		{
			$fallback = $this->theme->fallback();
			$theme = $fallback['name'];

			\Config::set('indigo.theme.' . $this->theme_type, $theme);
			\Config::save('indigo', 'indigo');
		}

		$this->theme->active($theme);

		// Sets theme extension
		if ($engine = $this->theme->get_info('engine'))
		{
			$this->theme->set_config('view_ext', '.' . $engine);
		}

		return $this->theme_init();
	}
}
