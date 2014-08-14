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
	/**
	 * {@inheritdoc}
	 */
	public function before()
	{
		parent::before();

		// Makes the site name available in all views
		$this->template->set_global('site_name', \Config::get('indigo.site_name', 'Indigo Admin'));

		// Makes logged in user available in all views
		$this->current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
		$this->template->set_global('current_user', $this->current_user, false);

		if ('twig' == $this->theme->get_info('engine'))
		{
			\Config::set('parser.View_Twig.views_paths', $this->theme->get_all_paths());
		}
	}
}
