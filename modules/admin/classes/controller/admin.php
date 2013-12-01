<?php

namespace Admin;

class Controller_Admin extends \Controller_Base
{

	public $template = 'admin/template';

	public $theme_type = 'admin';

	public function before($data = null)
	{
		parent::before($data);
		if (\Request::active()->controller !== 'Admin\Controller_Admin' or ! in_array(\Request::active()->action, array('login', 'logout')))
		{
			if (\Auth::check())
			{
				if ( ! \Auth::has_access('admin.view'))
				{
					\Session::set_flash('error', gettext('You are not authorized to use the administration panel.'));
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

		$val = \Validation::forge();

		if (\Input::method() == 'POST')
		{
			$val->add('username', gettext('E-mail, or username'))
				->add_rule('required');
			$val->add('password', gettext('Password'))
				->add_rule('required');

			$monolog = new \Monolog\Logger('firephp');
			$stream = new \Monolog\Handler\FirePHPHandler();
			$monolog->pushHandler($stream);


			if ($val->run())
			{
				$auth = \Auth::instance();

				// check the credentials. This assumes that you have the previous table created
				if (\Auth::check() or $auth->login())
				{
					$current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
					\Session::set_flash('success', sprintf(gettext('Welcome, %s!'), $current_user->fullname));

					if (isset($current_user->pushover))
					{
						$handler = new \Monolog\Handler\PushoverHandler('acQWKWqiRqnVh5TTZ6MhEQP6bynFgv', $current_user->pushover, sprintf(gettext('%s login'), \Config::get('app.site_name', 'Indigo Admin')));
						$log = new \Monolog\Logger('pushover');
						$log->pushHandler($handler);
						$log->emergency(sprintf(gettext('Logged in from %s, with %s'), \Input::ip(), $current_user->email));
					}

					\Response::redirect(\Input::get('uri') ? : \Uri::admin());
				}
				else
				{
					\Session::set_flash('error', gettext('Wrong credentials!'));
				}
			}
			else
			{
				\Session::set_flash('error', implode('<br>', $val->error()));
			// $monolog->log('WARNING', 'This is a test message');
			}
		}

		$this->template->title = gettext('Login');
		$this->template->content = $this->theme->view('admin/login', array('val' => $val), false);
	}

	/**
	 * The logout action.
	 *
	 * @access  public
	 * @return  void
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
