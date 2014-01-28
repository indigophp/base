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
			'gravatar'             => new Twig_Function_Function('Gravatar::forge'),
			'menu'                 => new Twig_Function_Function('Menu::render_menu'),
			'admin_menu'           => new Twig_Function_Function('Menu_Admin::render_menu'),
			'request'              => new Twig_Function_Function('Request::active'),
			'admin_url'            => new Twig_Function_Function('Uri::admin'),
			'default_img'          => new Twig_Function_Method($this, 'getDefaultImage'),
			'auth_get_screen_name' => new Twig_Function_Function('Auth::get_screen_name'),
			'auth_get_meta'        => new Twig_Function_Function('Auth::get_profile_fields'),
			'date'                 => new Twig_Function_Function('Date::forge'),
			'time_elapsed'         => new Twig_Function_Method($this, 'time_elapsed'),
		);
	}

	public function getFilters()
	{
		return array(
			'md5'         => new Twig_Filter_Function('md5'),
			'pluralize'   => new Twig_Filter_Function('Inflector::pluralize'),
			'bytes'       => new Twig_Filter_Function('Num::format_bytes'),
			'qty'         => new Twig_Filter_Function('Num::quantity'),
			'bool'        => new Twig_Filter_Function([$this, 'bool']),
			'attr'        => new Twig_Filter_Function('array_to_attr'),
			'date_format' => new Twig_Filter_Function([$this, 'dateFormat']),
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

	public function dateFormat($timestamp, $pattern_key = 'local', $timezone = null)
	{
		if (is_numeric($timestamp))
		{
			$date = \Date::forge($timestamp);
		}
		else
		{
			$date = \Date::create_from_string($timestamp);
		}

		return $date->format($pattern_key, $timezone);
	}

	public function time_elapsed($timestamp)
	{
		if (empty($timestamp)) {
			return null;
		}

		$time = new DateTime();
		$time->setTimestamp($timestamp);
		$diff = $time->diff(new DateTime());

		$elapsed = '';

		if ($diff->days > 0)
		{
			$elapsed .= str_replace('%d', $diff->days, ngettext("%d day", "%d days", $diff->days)) . ', ';
		}

		$elapsed .= $diff->format('%H:%I:%S');

		return $elapsed;
	}

	public function bool($value)
	{
		return is_bool($value) ? ($value ? 'true' : 'false') : $value;
	}
}