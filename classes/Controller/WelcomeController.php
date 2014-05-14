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
 * Welcome Controller
 *
 * @author Tamás Barta <barta.tamas.d@gmail.com>
 */
class WelcomeController extends \Controller_Base
{

	public $template = 'frontend/template';

	public function action_index()
	{
		$this->template->content = $this->theme->view('frontend/welcome/index');
	}

	public function action_hello()
	{
		$this->template->content = $this->theme->view('frontend/welcome/hello');
	}

	public function action_404()
	{
		$this->template->content = $this->theme->view('frontend/welcome/404');
	}
}