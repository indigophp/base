<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Admin;

/**
 * Admin Base Controller class
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class Controller_Base extends \Controller_Base
{
	/**
	 * Template name
	 *
	 * @var string
	 */
	public $template = 'admin/template';

	/**
	 * Theme type (admin or frontend)
	 *
	 * @var string
	 */
	public $theme_type = 'admin';

	public function before($data = null)
	{
		parent::before($data);

		$alert = \Logger::forge('alert');

		if (\Auth::check())
		{
			if (\Auth::has_access('admin.view') === false)
			{
				$alert->error(gettext('You are not authorized to use the administration panel.'));
				\Response::redirect('/');
			}
		}
		else
		{
			\Response::redirect(\Uri::admin() . 'login?uri=' . urlencode(\Uri::string()));
		}
	}
}
