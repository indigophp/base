<?php

namespace Auth;

class Controller_Admin_Ajax extends \Admin\Controller_Rest
{
	public function action_list()
	{
		if ( ! Auth::has_access('users.list'))
		{
			throw new \HttpForbiddenException();
		}
		$query = \Model\Auth_User::query()
			->where('id', '>', '0');

		$query->where('id', '>', '0');

		$all_items_count = $query->count();

		$query->related('metadata');
		$query->where('metadata.key', 'fullname');
		$query
			->limit(\Input::param('iDisplayLength'))
			->offset(\Input::param('iDisplayStart'));

		$columns = array(
			'id',
			'username',
			'email',
			'metadata.value',
			'group_id'
		);

		$order_by = array();
		for ($i = 0; $i < \Input::param('iSortingCols'); $i++)
		{
			$order_by[$columns[\Input::param('iSortCol_'.$i)]] = \Input::param('sSortDir_'.$i);
		}
		$query->order_by($order_by);
		$users = $query->get();
		return array(
			'sEcho' => \Input::param('sEcho'),
			'iTotalRecords' => $all_items_count,
			'iTotalDisplayRecords' => count($users),
			'aaData' => array_values(array_map(function($user) {
				return array(
					$user->id,
					'<img width="26" src="https://secure.gravatar.com/avatar/' . md5($user->email) . '?s=26&d='.urlencode(\Uri::create('assets/theme/img/icons/' . ($user->group_id == 6 ? 'admin' : ($user->group_id == 1 ? 'banned' : 'user') ) . '.png')).'" alt=""> ' . $user->username,
					$user->email,
					$user->fullname,
					$user->group->name,
					'<div class="hidden-print btn-group btn-group-sm" style="width:100px">'.
						(Auth::has_access('users.view_details') ? '<a href="'.(\Uri::admin() . 'auth/details/'.$user->id).'" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>' : '').
						((Auth::has_access('users.edit_other') or (Auth::has_access('users.edit_own') and $user->username == Auth::get_screen_name())) ? '<a href="'.(\Uri::admin() . 'auth/edit/'.$user->id).'" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>' : '').
						((Auth::has_access('users.delete') and $user->username != Auth::get_screen_name()) ? '<a href="'.(\Uri::admin() . 'auth/delete/'.$user->id).'" class="btn btn-default"><span class="glyphicon glyphicon-remove" style="color:#f55;"></span></a>' : '').
					'</div>'
				);
			}, $users))
		);
	}
}