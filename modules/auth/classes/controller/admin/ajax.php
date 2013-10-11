<?php


namespace Auth;

class Controller_Admin_Ajax extends \Controller_Rest
{
	public function get_list()
	{
		$users = \Model\Auth_User::query()
			->where('id', '>', '0')
			->get();
		return array(
			'sEcho' => count($users),
			'iTotalRecords' => count($users),
			'iTotalDisplayRecords' => count($users),
			'aaData' => array_values(array_map(function($user) {
				return array(
					$user->user_id,
					'<img src="https://secure.gravatar.com/avatar/' . md5($user->email) . '?s=26&d=mm" alt=""> ' . $user->username,
					$user->email,
					$user->fullname,
					$user->group->name,
					'<div class="btn-group btn-group-sm" style="width:100px">
						<a href="" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>
						<a href="" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>
						<a href="" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>
					</div>'
				);
			}, $users))
		);
	}
}