<?php

namespace Admin;

class Controller_Enum extends \Admin\Controller_Admin_Skeleton
{
	protected $_model = 'Model_Enum';

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

	public function has_access($access)
	{
		return \Auth::has_access('enum.enum[' . $access . ']');
	}

	public function query($options = array())
	{
		$query = parent::query()
			->related('default');

		if ( ! $this->has_access('all'))
		{
			$query->where('read_only', 0);
		}

		return $query;
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

		empty($data['default.name']) and $data['default.name'] = gettext('<i>None</i>');

		return $data;
	}

	protected function name()
	{
		return array(
			ngettext('enum', 'enums', 1),
			ngettext('enum', 'enums', 999),
		);
	}

	public function action_view($id = null)
	{
		parent::action_view($id);
		$model = $this->template->content->model;
		$model->active = $model->active == 1 ? gettext('Yes') : gettext('No');
		$model->read_only = $model->read_only == 1 ? gettext('Yes') : gettext('No');

		is_array($model->items) and usort($model->items, function($a, $b) {
			return ($a['sort'] < $b['sort']) ? -1 : 1;
		});

		// $this->template->content->items = \Model_Enum_Item::query()
		// 	->where('enum_id', $model->id)
		// 	->order_by('sort')
	}
}
