<?php

namespace Admin;

class Controller_Admin extends \Controller_Base
{
	public $template = 'admin/template';

	public $theme_type = 'admin';

	public function before($data = null)
	{
		parent::before($data);

		if (get_called_class() !== get_class() or ! in_array($this->request->action, array('login', 'logout')))
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
			$val->add(\Config::get('auth.username_post_key', 'username'), gettext('E-mail, or username'))
				->add_rule('required');
			$val->add(\Config::get('auth.password_post_key', 'password'), gettext('Password'))
				->add_rule('required');

			if ($val->run())
			{
				$auth = \Auth::instance();

				if ($auth->login())
				{
					$current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
					\Session::set_flash('success', strtr(gettext('Welcome, %fullname%!'), '%fullname%', $current_user->fullname));
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
