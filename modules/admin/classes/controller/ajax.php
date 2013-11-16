<?php

namespace Admin;

class Controller_Ajax extends Controller_Rest
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

		$all_items_count = $query->count();

		$query
			->rows_limit(\Input::param('iDisplayLength', 10))
			->rows_offset(\Input::param('iDisplayStart', 0));

		$columns = array(
			'id',
			'name',
			'count',
			'default.name',
			'active',
			'read_only',
		);

		$skip = array(
			'count'
		);

		$order_by = array();
		for ($i = 0; $i < \Input::param('iSortingCols'); $i++)
		{
			if (\Input::param('bSortable_'.$i, false) and ! in_array($columns[$i], $skip))
			{
				$order_by[$columns[\Input::param('iSortCol_'.$i)]] = \Input::param('sSortDir_'.$i);
			}
		}
		$query->order_by($order_by);

		for ($i=0; $i < count($columns); $i++)
		{
			$filter = \Input::param('sSearch_'.$i);

			if (
				(isset($filter) and in_array($filter, array(null, '', 'null'))) or
				($columns[$i] == 'read_only' and ! \Auth::has_access('enums.all')) or
				\Input::param('bSearchable_'.$i, false) == false or
				in_array($columns[$i], $skip)
			)
			{
				continue;
			}

			if (strpos($filter, ','))
			{
				$query->where($columns[$i], 'IN', explode(',', $filter));
			}
			else
			{
				$query->where($columns[$i], 'LIKE', '%' . $filter . '%');
			}
		}

		$enums = $query->get();

		return array(
			'sEcho' => \Input::param('sEcho'),
			'iTotalRecords' => $all_items_count,
			'iTotalDisplayRecords' => count($enums),
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