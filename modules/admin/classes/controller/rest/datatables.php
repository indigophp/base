<?php

namespace Admin;

class Controller_Rest_Datatables extends Controller_Rest
{
	protected function process_query(\Orm\Query $query, array $columns = array(), array $defaults = array())
	{
		// Count all items
		$all_items_count = $query->count();

		// Process incoming sortng values
		$sort = array();
		for ($i = 0; $i < \Input::param('iSortingCols'); $i++)
		{
			$sort[\Input::param('iSortCol_'.$i)] = \Input::param('sSortDir_'.$i);
		}

		$i = 0;
		$order_by = array();
		$where = array();
		$global_filter = \Input::param('sSearch');

		foreach ($columns as $key => $value)
		{
			if (is_int($key))
			{
				$key = $value;
				$value = array();
			}
			$value = \Arr::merge($defaults, $value);

			if (\Input::param('bSortable_'.$i, true) and \Arr::get($value, 'sort', true) and array_key_exists($i,  $sort))
			{
				$order_by[$key] = $sort[$i];
			}

			$filter = \Input::param('sSearch_'.$i);

			$filter = json_decode($filter);

			if ( ! in_array($filter, array(null, '', 'null')) and \Input::param('bSearchable_'.$i, true) and \Arr::get($value, 'search', true))
			{
				switch (\Arr::get($value, 'type', 'text'))
				{
					case 'select-multiple':
					case 'select':
						$query->where($key, 'IN', $filter);
						break;
					case 'select-single':
					case 'number':
						$query->where($key, $filter);
						break;
					case 'text':
						$query->where($key, 'LIKE', '%' . $filter . '%');
						break;
					case 'range':
						$query->where($key, 'BETWEEN', $filter);
						break;
					default:
						break;
				}
			}

			if ( ! empty($global_filter))
			{
				if (\Arr::get($value, 'search', true) === true and \Arr::get($value, 'global', true) === true)
				{
					$where[] = array($key, 'LIKE', '%' . $global_filter . '%');
				}
			}

			$i++;
		}

		if ( ! empty($where))
		{
			$query->where_open();
			foreach ($where as $where)
			{
				$query->or_where($where[0], $where[1], $where[2]);
			}
			$query->where_close();
		}
		// Order query
		$query->order_by($order_by);

		// Count the filtered dataset
		$items_count = $query->count();

		// Limit query
		$query
			->rows_limit(\Input::param('iDisplayLength', 10))
			->rows_offset(\Input::param('iDisplayStart', 0));

		return array($all_items_count, $items_count);
	}
}
