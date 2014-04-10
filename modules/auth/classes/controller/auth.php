<?php

namespace Auth;

use Fuel\Validation\Validator;

class Controller_Auth extends \Controller_Base
{
	public $template = 'frontend/template';

	public $theme_type = 'frontend';

	public function action_index()
	{
		return \Response::redirect('/auth/login');
	}

	public function redirect()
	{
		$uri = \Input::get('uri');

		return \Response::redirect(isset($uri) ? $uri : '/user');
	}

	public function action_login()
	{
		// Already logged in
		\Auth::check() and $this->redirect();

		$this->template->title = gettext('Login');
		$this->template->content = $this->theme->view('auth/login');
	}

	public function post_login()
	{
		// Already logged in
		\Auth::check() and $this->redirect();

		$validator = new Validator;

		$validator->addField(
			\Config::get('auth.username_post_key', 'username'),
			gettext('E-mail, or username')
		)->required();

		$validator->addField(
			\Config::get('auth.password_post_key', 'password'),
			gettext('Password')
		)->required();

		$result = $validator->run(\Input::post());

		if ($result->isValid())
		{
			$auth = \Auth::instance();

			if ($auth->login())
			{
				if (\Input::post('remember_me', false))
				{
					\Auth::remember_me();
				}
				else
				{
					\Auth::dont_remember_me();
				}

				$current_user = \Model\Auth_User::find_by_username(\Auth::get_screen_name());
				\Session::set_flash('success', \Str::trans(
					gettext('Welcome, %fullname%!'),
					'%fullname%',
					$current_user->fullname
				));

				return $this->redirect();
			}
			else
			{
				\Session::set_flash('error', gettext('Wrong credentials!'));
			}
		}
		else
		{
			\Session::set_flash('errors', $result->getErrors());
		}

		return \Reponse::redirect();
	}

	public function action_logout()
	{
		\Auth::logout();
		\Response::redirect('/auth');
	}

	public function action_oauth($provider = null)
	{
		\Auth_Opauth::forge();
	}

	public function action_callback()
	{
		// Opauth can throw all kinds of nasty bits, so be prepared
		try
		{
			// get the Opauth object
			$opauth = \Auth_Opauth::forge(false);

			// and process the callback
			$status = $opauth->login_or_register();

			// fetch the provider name from the opauth response so we can display a message
			$provider = $opauth->get('auth.provider', '?');

			// deal with the result of the callback process
			switch ($status)
			{
				// a local user was logged-in, the provider has been linked to this user
				case 'linked':
					// inform the user the link was succesfully made
					\Messages::success(sprintf(__('login.provider-linked'), ucfirst($provider)));
					// and set the redirect url for this status
					$url = 'auth/user';
				break;

				// the provider was known and linked, the linked account as logged-in
				case 'logged_in':
					// inform the user the login using the provider was succesful
					\Messages::success(sprintf(__('login.logged_in_using_provider'), ucfirst($provider)));
					// and set the redirect url for this status
					$url = 'auth/user';
				break;

				// we don't know this provider login, ask the user to create a local account first
				case 'register':
					// inform the user the login using the provider was succesful, but we need a local account to continue
					\Messages::info(sprintf(__('login.register-first'), ucfirst($provider)));
					// and set the redirect url for this status
					$url = 'auth/register';
				break;

				// we didn't know this provider login, but enough info was returned to auto-register the user
				case 'registered':
					// inform the user the login using the provider was succesful, and we created a local account
					\Messages::success(__('login.auto-registered'));
					// and set the redirect url for this status
					$url = 'auth/user';
				break;

				default:
					throw new \FuelException('Auth_Opauth::login_or_register() has come up with a result that we dont know how to handle.');
			}

			// redirect to the url set
			\Response::redirect($url);
		}

		// deal with Opauth exceptions
		catch (\OpauthException $e)
		{
			\Messages::error($e->getMessage());
			\Response::redirect_back();
		}

		// catch a user cancelling the authentication attempt (some providers allow that)
		catch (\OpauthCancelException $e)
		{
			// you should probably do something a bit more clean here...
			exit('It looks like you canceled your authorisation.'.\Html::anchor('users/oath/'.$provider, 'Click here').' to try again.');
		}

	}
}
