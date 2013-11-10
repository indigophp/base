<?php

namespace Auth;

class Controller_Widgets extends \Admin\Controller_Admin
{

	public function action_dashboard()
	{
		$widget = $this->theme->view('admin/user/dashboard_widget');

		$widget->num_users = Model\Auth_User::query()->count() -1;
		$widget->num_admins = Model\Auth_User::query()->where('group_id', 6)->count();
		$widget->num_new = Model\Auth_User::query()->where('created_at', '>', strtotime('-1 week'))->count();
		$widget->latest = Model\Auth_User::query()->limit(1)->order_by('created_at', 'DESC')->get_one();

		return \Response::forge($widget->render(), 200);
	}

}