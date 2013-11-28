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
			'request'         => new Twig_Function_Function('Request::active'),
			'admin_url'       => new Twig_Function_Function('Uri::admin'),
			'current_url'     => new Twig_Function_Function('Uri::current'),
			'default_img'     => new Twig_Function_Method($this, 'getDefaultImage'),
		);
	}

	public function getFilters()
	{

		return array(
			'md5'       => new Twig_Filter_Function('md5'),
			'pluralize' => new Twig_Filter_Function('Inflector::pluralize'),
			'bytes'     => new Twig_Filter_Function('Num::format_bytes'),
			'qty'       => new Twig_Filter_Function('Num::quantity'),
			'bool'      => new Twig_Filter_Function([$this, 'bool']),
			'attr'      => new Twig_Filter_Function('array_to_attr'),
		);
	}

	public function getTests()
	{
		return array(
			'bool' => new Twig_Test_Function('is_bool')
		);
	}

	// base_url() ~ 'assets/theme/img/icons/' ~ (model.group_id == 6 ? 'admin' : model.group_id == 1 ? 'user_cancel' : 'user') ~ '.png' | url_encode
	public function getDefaultImage(\Auth\Model\Auth_User $model)
	{
		return urlencode(Uri::create('assets/theme/img/icons/' . ($model->group_id == 6 ? 'admin' : ($model->group_id == 1 ? 'banned' : 'user') ) . '.png'));
	}

	public function bool($value)
	{
		return is_bool($value) ? ($value ? 'true' : 'false') : $value;
	}
}