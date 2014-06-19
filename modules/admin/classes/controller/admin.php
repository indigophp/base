<?php

namespace Admin;

use Fuel\Validation\Validator;

class Controller_Admin extends Controller_Base
{
	public function before($data = null)
	{
		if (in_array($this->request->action, array('login', 'logout')))
		{
			$alert = \Logger::forge('alert');
			\Controller_Base::before($data);
		}
		else
		{
			parent::before($data);
		}
	}

	public function action_login()
	{
		if (\Auth::check())
		{
			return \Response::redirect(\Input::get('uri', \Uri::admin()));
		}

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

					\Logger::instance('alert')->info(gettext('Welcome, %fullname%!'), $context);

					return \Response::redirect(\Input::get('uri', \Uri::admin()));
				}
				else
				{
					\Logger::instance('alert')->error(gettext('Wrong credentials!'));
				}
			}
			else
			{
				$context = array(
					'errors' => $result->getErrors(),
				);

				\Logger::instance('alert')->error(gettext('There were some errors.'), $context);
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
		return \Response::redirect(\Uri::admin());
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
