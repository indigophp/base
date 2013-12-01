<?php

namespace Admin;

class Controller_Enum_Item extends \Admin\Controller_Admin_Skeleton
{
	protected $_enum;

	public static function _init()
	{
		static::$translate = array(
			'create' => array(
				'access' => gettext('You are not authorized to paste wisecracks.')
			),
			'details' => array(
				'access' => gettext('You are not authorized to view wisecracks.')
			),
			'edit' => array(
				'access' => gettext('You are not authorized to edit wisecracks.')
			),
			'delete' => array(
				'access' => gettext('You are not authorized to delete wisecracks.')
			)
		);
	}

	protected function model()
	{
		return 'Model_Enum_Item';
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
		$query = parent::query()
			->where('enum_id', $this->param('enum_id'));

		if ( ! \Auth::has_access('enum.all'))
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

		$query = \Model_Enum::query()
			->where('id',  $id);

		if ( ! \Auth::has_access('enum.all'))
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

		return $this->_url = parent::url() . $this->param('enum_id') . '/';

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
