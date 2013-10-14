<?php


namespace Auth;

class Controller_Admin_Ajax extends \Controller_Rest
{

	public function before($data = null)
	{
		parent::before($data);
		if (\Request::active()->controller !== 'Admin\Controller_Admin' or ! in_array(\Request::active()->action, array('login', 'logout'))) {

			if (\Auth::check())
			{
				if ( ! \Auth::has_access('view_admin'))
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

	public function get_list()
	{
		if (!Auth::has_access('users.list'))
		{
			return HttpForbiddenException();
		}
		$users = \Model\Auth_User::query()
			->where('id', '>', '0')
			->get();
		return array(
			'sEcho' => count($users),
			'iTotalRecords' => count($users),
			'iTotalDisplayRecords' => count($users),
			'aaData' => array_values(array_map(function($user) {
				return array(
					$user->id,
					'<img src="https://secure.gravatar.com/avatar/' . md5($user->email) . '?s=26&d=mm" alt=""> ' . $user->username,
					$user->email,
					$user->fullname,
					$user->group->name,
					'<div class="btn-group btn-group-sm" style="width:100px">'.
						(Auth::has_access('users.view_details') ? '<a href="'.\Uri::create('admin/auth/details/'.$user->id).'" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>' : '').
						((Auth::has_access('users.edit_other') or (Auth::has_access('users.edit_own') and $user->username == Auth::get_screen_name())) ? '<a href="'.\Uri::create('admin/auth/edit/'.$user->id).'" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>' : '').
						((Auth::has_access('users.delete') and $user->username != Auth::get_screen_name()) ? '<a href="'.\Uri::create('admin/auth/delete/'.$user->id).'" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>' : '').
					'</div>'
				);
			}, $users))
		);
	}
}