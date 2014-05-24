<?php

namespace Admin;

use Fuel\Validation\Validator;
use Monolog\Logger;
use Monolog\Handler\AlertHandler;

class Controller_Admin extends \Controller_Base
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

	/**
	 * Alert logger object
	 *
	 * @var Logger
	 */
	protected $alert;

	public function before($data = null)
	{
		parent::before($data);

		$this->alert = new Logger('alert');
		$this->alert->pushHandler(new AlertHandler);

		if (get_called_class() !== get_class() or ! in_array($this->request->action, array('login', 'logout')))
		{
			if (\Auth::check())
			{
				if ( ! \Auth::has_access('admin.view'))
				{
					$this->alert->error(gettext('You are not authorized to use the administration panel.'));
					\Response::redirect('/');
				}
			}
			else
			{
				\Response::redirect(\Uri::admin() . 'login?uri=' . urlencode(\Uri::string()));
			}
		}
	}

	public function action_login()
	{
		// Already logged in
		\Auth::check() and \Response::redirect(\Input::get('uri') ? : \Uri::admin());

		if (\Input::method() == 'POST')
		{
			$val = new Validator;

			$val->addField(\Config::get('auth.username_post_key', 'username'), gettext('E-mail, or username'))
				->required();
			$val->addField(\Config::get('auth.password_post_key', 'password'), gettext('Password'))
				->required();

			$result = $val->run(\Input::post());

			if ($result->isValid())
			{
				$auth = \Auth::instance();

				if ($auth->login())
				{
					$current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());

					$context = array(
						'template' => 'success',
						'from'     => '%fullname%',
						'to'       => $current_user->fullname,
					);

					$this->alert->info(gettext('Welcome, %fullname%!'), $context);

					\Response::redirect(\Input::get('uri') ? : \Uri::admin());
				}
				else
				{
					$this->alert->error(gettext('Wrong credentials!'));
				}
			}
			else
			{
				$context = array(
					'errors' => $result->getErrors(),
				);

				$this->alert->error(gettext('There were some errors.'), $context);
			}
		}

		$this->template->title = gettext('Login');
		$this->template->content = $this->theme->view('admin/login');
	}

	/**
	 * The logout action
	 */
	public function action_logout()
	{
		\Auth::logout();
		\Response::redirect(\Uri::admin());
	}

	public function action_index()
	{
		$this->template->content = $this->theme->view('admin/dashboard');

		$widgets = array();

		foreach (\Module::loaded() as $name => $path)
		{
			$class_name = \Inflector::words_to_upper($name) . '\\Controller_Widgets';
			if (class_exists($class_name) and in_array('action_dashboard', get_class_methods($class_name)))
			{
				$widgets[] = \Request::forge($name.'/widgets/dashboard', false)->execute()->response()->body();
			}
		}

		$this->template->content->set('widgets', $widgets, false);

	}

}
