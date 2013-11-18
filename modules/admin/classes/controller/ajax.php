<?php

namespace Admin;

class Controller_Ajax extends Controller_Rest_Datatables
{
	public function action_enums()
	{
		if ( ! \Auth::has_access('enums.list'))
		{
			throw new \HttpForbiddenException();
		}

		$query = \Model_Enum::query()->related('default')->related('items');

		if ( ! \Auth::has_access('enums.all'))
		{
			$query->where('read_only', 0);
		}

		// Column definitions
		$columns = array(
			'id' => array(
				'type' => 'number'
			),
			'name',
			'count' => array(
				'sort' => false,
				'search' => false
			),
			'default.name',
			'active' => array(
				'type' => 'select-multiple'
			),
			'read_only' => array(
				'search' => \Auth::has_access('enums.all'),
				'sort' => \Auth::has_access('enums.all'),
				'type' => 'select-multiple',
			),
		);

		$counts = $this->process_query($query, $columns);

		$enums = $query->get();

		return array(
			'sEcho' => \Input::param('sEcho'),
			'iTotalRecords' => $counts[0],
			'iTotalDisplayRecords' => $counts[1],
			'aaData' => array_values(array_map(function($enum) {
				return array(
					$enum->id,
					$enum->name,
					count($enum->items),
					$enum->default ? $enum->default->name : '<i>' . gettext('None') . '</i>',
					gettext($enum->active == true ? 'Yes' : 'No'),
					gettext($enum->read_only == true ? 'Yes' : 'No'),
					'<div class="hidden-print btn-group btn-group-sm" style="width:100px">'.
						(\Auth::has_access('enums.view_details') ? '<a href="'.\Uri::create('admin/enums/details/'.$enum->id).'" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>' : '').
						((\Auth::has_access('enums.edit') and ($enum->read_only == false or \Auth::has_access('enums.all'))) ? '<a href="'.\Uri::create('admin/enums/edit/'.$enum->id).'" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>' : '').
						((\Auth::has_access('enums.all') and ($enum->read_only == false or \Auth::has_access('enums.all'))) ? '<a href="'.\Uri::create('admin/enums/delete/'.$enum->id).'" class="btn btn-default"><span class="glyphicon glyphicon-remove" style="color:#f55;"></span></a>' : '').
					'</div>'
				);
			}, $enums))
		);
	}
}