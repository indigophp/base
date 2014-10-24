<?php

/*
 * This file is part of the IndigoPHP framework.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Controller;

class Welcome extends \Fuel\Controller\Base
{
	/**
	 * The basic welcome message
	 *
	 * @return \View
	 */
	public function actionIndex()
	{
		// var_dump(\Application::getInstance()->getViewManager()->getFinder()); exit;
		return \View::forge('welcome/index.twig');
	}

	/**
	 * The 404 action for the application.
	 *
	 * @return \Response
	 */
	public function action404()
	{
		return \Response::forge('html', \View::forge('welcome/404.twig'), 404);
	}

}
