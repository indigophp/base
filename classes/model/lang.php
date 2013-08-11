<?php

namespace Orm;

class Model_Lang extends Model
{
	/**
	 * Language data
	 * @var array
	 */
	protected static $_language;

	protected static $_eav_temp;

	public function _init()
	{
		static::$_has_many['lang'] => array(
			'key_from' => 'id',
			'key_to' => 'item_id',
			'model_to' => 'Model_Lang_Item',
			'conditions' => array(
				array('model', '=', \Inflector::denamespace(get_called_class())),
				array('lang', '=', \Lang::get_lang())
			)
		);

		static::$_eav['lang'] = array(
			'attribute' => 'key',
			'value' => 'value'
		);
	}

	private static function disable_eav()
	{
		static::$_eav_temp = static::$_eav;

		static::$_eav = array(
			'lang' => static::$_eav['lang']
		);
	}

	private static function enable_eav()
	{
		static::$_eav = static::$_eav_temp;
	}

	public function & get($property)
	{
		if (in_array($property, static::$_lang_fields) && \Lang::get_lang() !== 'en')
		{
			return static::$_language[$this->id][$property];
		}

		return parent::get($property);
	}

	public function set($property, $value = null)
	{
		// check if we're in a frozen state
		if ($this->_frozen)
		{
			throw new FrozenObject('No changes allowed.');
		}

		if (is_array($property))
		{
			foreach ($property as $p => $v)
			{
				$this->set($p, $v);
			}
		}
		else
		{
			if (in_array($property, static::$_lang_fields) && ! empty(static::$_language[$this->id][$property]) && \Lang::get_lang() !== 'en')
			{

				if (func_num_args() < 2)
				{
					throw new \InvalidArgumentException('You need to pass both a property name and a value to set().');
				}

				static::$_language[$this->id][$property] = $value;
				return $this;
			}

			return parent::set($property, $value);
		}

	}

	final public static function generate_cache($model = null, $lang = null)
	{
		! is_string($lang) && $lang = \Lang::get_lang();

		if ($model === 'all')
		{
			$classes = get_declared_classes();

			foreach ($classes as $model)
			{
				if (is_subclass_of($class, 'Orm\Model_Lang')) {
					static::_generate_cache($model, $lang);
				}
			}
		}
		elseif (is_array($model))
		{
			foreach ($model as $m)
			{
				static::_generate_cache($m, $lang);
			}
		}
		else
		{
			return static::_generate_cache(get_called_class(), $lang);
		}

		return true;
	}

	final protected static function _generate_cache($model, $lang)
	{
		$model = \Inflector::denamespace($model);

		$language = \DB::select('id', 'item_id', 'key', 'value')
			->from('language')
			->where('lang', $lang)
			->where('model', $class)
			->execute()
			->as_array();

			static::$_language = array();

		foreach ($language as $value)
		{
			static::$_language[$value['item_id']][$value['key']] = $value['value'];
		}

		\Cache::set('language.' . $lang . '.' . $class, static::$_language);
		return true;
	}
}
