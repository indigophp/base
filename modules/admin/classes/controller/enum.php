<?php

namespace Admin;

class Controller_Enum extends \Admin\Controller_Admin_Skeleton
{
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

	public function query($options = array())
	{
		$query = parent::query();

		if ( ! \Auth::has_access('enum.all'))
		{
			$query->where('read_only', 0);
		}

		return $query;
	}

	protected function model()
	{
		return 'Model_Enum';
	}

	protected function view($view, $data = array(), $auto_filter = null)
	{
		switch ($this->request->action)
		{
			case 'view':
				$view = 'admin/enum/view';
				break;
			default:
				break;
		}

		return parent::view($view, $data, $auto_filter);
	}

	protected function map(\Orm\Model $model, array $properties)
	{
		$data = parent::map($model, $properties);

		empty($data[1]) and $data[1] = gettext('<i>None</i>');
		$data[2] = $data[2] ? gettext('Yes') : gettext('No');
		$data[3] = $data[3] ? gettext('Yes') : gettext('No');

		return $data;
	}

	protected function name()
	{
		return array(
			ngettext('enum', 'enums', 1),
			ngettext('enum', 'enums', 2),
		);
	}

	public function action_view($id = null)
	{
		parent::action_view($id);

		$this->theme->content->item = ngettext('enum item', 'enum items', 1);
	}
}
