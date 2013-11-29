<?php

namespace Indigo\Base;

class Model_Enum extends \Orm\Model
{
	use \Admin\Model_Skeleton;

	protected static $_has_many = array(
		'items' => array(
			'model_to'       => 'Model_Enum_Item',
			'cascade_delete' => true,
		),
	);

	protected static $_has_one = array(
		'default' => array(
			'key_from' => array('id', 'default_id'),
			'key_to'   => array('enum_id', 'item_id'),
			'model_to' => 'Model_Enum_Item',
		),
	);

	protected static $_observers = array(
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert')
		),
		'Orm\\Observer_Slug' => array(
			'events' => array('before_insert'),
			'source' => 'name',
		),
	);

	protected static $_properties = array(
		'id' => array(
			'view' => false
		),
		'name' => array(
			'form' => array('type' => 'text'),
			'list' => array('type' => 'text'),
			'validation' => 'required|trim'
		),
		'slug' => array(),
		'description' => array(
			'form' => array('type' => 'textarea'),
		),
		'default_id' => array(
			'default'   => 1,
			'data_type' => 'int',
			'details' => false,
			'form' => array('type' => 'select'),
		),
		'default.name' => array(
			'list' => array('type' => 'text'),
		),
		'active' => array(
			'default'   => 1,
			'data_type' => 'int',
			'min'       => 0,
			'max'       => 1,
			'form'      => array('type' => 'switch'),
			'list' => array('type' => 'select'),
		),
		'read_only' => array(
			'default'   => 0,
			'data_type' => 'int',
			'min'       => 0,
			'max'       => 1,
			'list' => array(
				'type'    => 'select',
				'default' => 0
			),
		),
	);

	protected static $_table_name = 'enums';

	public static function _init()
	{
		static::$_properties = \Arr::merge(static::$_properties, array(
			'id' => array('label' => gettext('ID')),
			'name' => array('label' => gettext('Name')),
			'slug' => array('label' => gettext('Slug')),
			'description' => array('label' => gettext('Description')),
			'default_id' => array(
				'label' => gettext('Default'),
				'form' => array(
					'options' => function($model) {
						$model->items;
						$model = $model->to_array();
						return \Arr::pluck($model['items'], 'name', 'id');
					}
				)
			),
			'default.name' => array('label' => gettext('Default')),
			'active' => array(
				'label' => gettext('Active'),
				'form' => array(
					'options' => array(
						0 => gettext('No'),
						1 => gettext('Yes'),
					),
				),
			),
			'read_only' => array('label' => gettext('Read-only')),
		));

		if (\Auth::has_access('enum.all'))
		{
			\Arr::set(static::$_properties, 'read_only.form', array(
					'type' => 'switch',
					'options' => array(
						0 => gettext('No'),
						1 => gettext('Yes'),
					),
			));
		}
	}

	public function add_item($data = array(), $default = false, $save = true)
	{
		if (\Arr::is_multi($data))
		{
			foreach ($data as $default => $item)
			{
				$this->add_item($item, $default === 'deafult', false);
			}
		}
		else
		{
			$model = \Model_Enum_Item::forge();
			$model->set($data);
			$this->items[] = $model;

			if ($default === true)
			{
				$this->default = $model;
			}
		}

		if ($save === true)
		{
			$this->save();
		}
	}
}
