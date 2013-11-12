<?php

class Twig_Indigo_Extension extends Twig_Extension
{

	/**
	 * Gets the name of the extension.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'indigo';
	}

	public function getFunctions()
	{
		return array(
			'auth_has_access' => new Twig_Function_Function('Auth::has_access'),
			'gravatar'        => new Twig_Function_Function('Gravatar::forge'),
			'menu'            => new Twig_Function_Function('Menu::render_menu'),
			'admin_menu'      => new Twig_Function_Function('Menu_Admin::render_menu'),
			'default_img'     => new Twig_Function_Method($this, 'getDefaultImage'),
		);
	}

	public function getFilters()
	{

		return array(
			'md5'      => new Twig_Filter_Function('md5'),
		);
	}

	// base_url() ~ 'assets/theme/img/icons/' ~ (model.group_id == 6 ? 'admin' : model.group_id == 1 ? 'user_cancel' : 'user') ~ '.png' | url_encode
	public function getDefaultImage(\Auth\Model\Auth_User $model)
	{
		return urlencode(Uri::create('assets/theme/img/icons/' . ($model->group_id == 6 ? 'admin' : ($model->group_id == 1 ? 'banned' : 'user') ) . '.png'));
	}

}