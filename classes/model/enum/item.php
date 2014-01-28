<?php

namespace Indigo\Base;

class Model_Enum_Item extends \Orm\Model
{
	use \Admin\Model_Skeleton;

	protected static $_belongs_to = array(
		'enum' => array(
			'model_to' => 'Model_Enum',
		)
	);

	protected static $_eav = array(
		'meta' => array(
			'attribute' => 'key',
			'value'     => 'value',
		)
	);

	protected static $_has_many = array(
		'meta' => array(
			'model_to'       => 'Model_Enum_Meta',
			'key_to'         => 'item_id',
			'cascade_delete' => true,
		),
	);

	protected static $_observers = array(
		'Orm\\Observer_Typing',
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert')
		)
	);

	protected static $_properties = array(
		'pk' => array(),
		'id' => array(),
		'enum_id' => array('data_type' => 'int'),
		'name' => array(
			'form' => array('type' => 'text'),
			'validation' => 'required|trim',
		),
		'slug' => array(),
		'description' => array(
			'form' => array('type' => 'textarea'),
		),
		'active' => array(
			'default'   => 1,
			'data_type' => 'boolean',
			'min'       => 0,
			'max'       => 1,
			'form'      => array(
				'type' => 'switch'
			),
		),
		'sort' => array('data_type' => 'int'),
	);

	protected static $_sort = true;

	protected static $_table_name = 'enum_items';

	protected static $_primary_key = array('pk');

	public static function _init()
	{
		static::$_properties = \Arr::merge(static::$_properties, array(
			'pk' => array(
				'label' => gettext('Item ID')
			),
			'id' => array(
				'label' => gettext('ID')
			),
			'enum_id' => array(
				'label' => gettext('Enum ID')
			),
			'name' => array(
				'label' => gettext('Name'),
			),
			'slug' => array(
				'label' => gettext('Slug'),
			),
			'description' => array(
				'label' => gettext('Description'),
			),
			'active' => array(
				'label' => gettext('Active'),
				'form' => array(
					'options' => array(
						0 => gettext('No'),
						1 => gettext('Yes'),
					),
				),
			),
			'sort' => array(
				'label' => gettext('Sort'),
			),
		));
	}

	public function _event_before_insert()
	{
		$this->id = $this->query()->where('enum_id', $this->enum_id)->max('id') + 1;
		static::$_sort === true and $this->sort = $this->query()->where('enum_id', $this->enum_id)->max('sort') + 10;
		$this->slug = \Inflector::friendly_title($this->name, '_', true);
	}

	public static function query($options = array())
	{
		$query = parent::query($options);

		if ( ! empty(static::$_enum))
		{
			// $query
			// ->related('enum')
			// ->where('enum.slug', static::$_enum);
			// $query->related('enum')->where('enum.slug', static::$_enum);
			// TODO: this is a bit messy
			// $query->where('enum_id', \Model_Enum::query()->select('id')->where('slug', static::$_enum)->rows_limit(1)->get_query(true));
		}

		return $query;
	}

	public static function forge($data = array(), $new = true, $view = null, $cache = true)
	{
		$model = parent::forge($data, $new, $view, $cache);

		if ( ! empty(static::$_enum))
		{
			$model->set('enum', static::enum());
		}

		return $model;
	}

	public static function enum()
	{
		if ( ! empty(static::$_enum))
		{
			return \Model_Enum::find_by_slug(static::$_enum);
		}
	}
}
