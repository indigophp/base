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

use Indigo\Base\Controller\AssetsController;

/**
 * Admin Assets Controller class
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Controller_Assets extends AssetsController
{
	public function before($data = null)
	{
		parent::before($data);

		$this->theme->active(\Config::get('base.theme.admin', 'default'));
	}

	public function get_url()
	{
		// We need the URL to know what to serve
		$segments = \Uri::segments();
		array_shift($segments);
		array_shift($segments);
		array_shift($segments);

		$url = implode('/', $segments) . '.' . \Input::extension();

		if(false !== strpos($url, '..'))
		{
			throw new \HttpForbiddenException();
		}

		return $url;
	}
}
