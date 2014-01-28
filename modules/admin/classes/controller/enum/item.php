<?php

namespace Admin;

class Controller_Enum_Item extends \Admin\Controller_Admin_Skeleton
{
	protected $_enum;

	protected $_model = 'Model_Enum_Item';

	public static function _init()
	{
		static::$translate = array(
			'create' => array(
				'access' => gettext('You are not authorized to paste enum items.')
			),
			'details' => array(
				'access' => gettext('You are not authorized to view enum items.')
			),
			'edit' => array(
				'access' => gettext('You are not authorized to edit enum items.')
			),
			'delete' => array(
				'access' => gettext('You are not authorized to delete enum items.')
			)
		);
	}

	protected function name()
	{
		return array(
			ngettext('enum item', 'enum items', 1),
			ngettext('enum item', 'enum items', 999),
		);
	}

	public function query($options = array())
	{
		$query = parent::query();

		$enum_id = $this->param('enum_id');

		if (is_numeric($enum_id))
		{
			$query->where('enum_id', $enum_id);
		}
		else
		{
			$query->related('enum')
				->where('enum.slug', $enum_id);
		}
			// ->where('enum_id', \Model_Enum::query()->select('id')->where('slug', $this->param('enum_id'))->rows_limit(1)->get_query(true));

		if ( ! \Auth::has_access('enum.enum[all]'))
		{
			$query->where('enum.read_only', 0);
		}

		return $query;
	}

	protected function enum($id = null)
	{
		if ($this->_enum instanceof \Model_Enum and is_null($id))
		{
			return $this->_enum;
		}

		$query = \Model_Enum::query();

		if (is_numeric($id))
		{
			$query->where('id', $id);
		}
		else
		{
			$query->where('slug', $id);
		}

		if ( ! \Auth::has_access('enum.enum[all]'))
		{
			$query->where('read_only', 0);
		}

		if (is_null($id) or is_null($model = $query->get_one()))
		{
			throw new \HttpNotFoundException();
		}

		return $this->_enum = $model;
	}

	protected function forge($data = array(), $new = true, $view = null, $cache = true)
	{
		$model = parent::forge($data, $new, $view, $cache);
		$model->enum = $this->enum($this->param('enum_id'));
		return $model;
	}

	protected function url()
	{
		if ( ! empty($this->_url))
		{
			return $this->_url;
		}

		return $this->_url = parent::url() . '/' . $this->param('enum_id') . '/';

	}

	public function action_index()
	{
		return $this->redirect(\Uri::admin() . 'enum/view/' . $this->param('enum_id'));
	}

	public function action_view($id = null)
	{
		return $this->action_index();
	}
}
