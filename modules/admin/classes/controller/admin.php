<?php

namespace Admin;

class Controller_Admin extends \Controller_Base
{

	public function before($data = null)
	{
		parent::before($data);
		if (\Request::active()->controller !== 'Admin\Controller_Admin' or ! in_array(\Request::active()->action, array('login', 'logout'))) {

			if (\Auth::check())
			{
				if ( ! \Auth::member(6))
				{
					\Session::set_flash('error', e('You are not authorized to use the administration panel.'));
					\Response::redirect('/');
				}
			}
			else
			{
				\Response::redirect('admin/login?uri=' . urlencode(\Uri::current()));
			}
		}
	}

	public function action_login()
	{
		// Already logged in
		\Auth::check() and \Response::redirect(\Input::get('uri') ? : 'admin');

		$val = \Validation::forge();

		if (\Input::method() == 'POST')
		{
			$val->add('username', 'E-mail, or username')
				->add_rule('required');
			$val->add('password', 'Password')
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
					\Session::set_flash('success', e('Welcome, '.$current_user->fullname.'!'));
					\Response::redirect(\Input::get('uri') ? : 'admin');
				}
				else
				{
					\Session::set_flash('error', 'Wrong credentials!');
				}
			}
			else
			{
				\Session::set_flash('error', implode('<br>', $val->error()));
			// $monolog->log('WARNING', 'This is a test message');
			}
		}

		$this->template->title = 'Login';
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
		\Response::redirect('admin');
	}

	public function action_index()
	{
		$this->template->content = $this->theme->view('dashboard');
	}

}
