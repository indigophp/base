<?php

namespace Orm;

class Model_Lang extends Model
{
	protected static $_language;

	protected static $_lang_fields;

	public static function _init()
	{

		$class = \Inflector::denamespace(get_called_class());
		try
		{
			static::$_language = \Cache::get('language.' . \Lang::get_lang() . '.' . $class);
		}
		catch (\CacheNotFoundException $e)
		{

			$language = \DB::select('id', 'item_id', 'key', 'value')
				->from('language')
				->where('lang', \Lang::get_lang())
				->where('model', $class)
				->execute()
				->as_array();

				static::$_language = array();

			foreach ($language as $key => $value)
			{
				static::$_language[$value['item_id']][$value['key']] = $value['value'];
			}
			\Cache::set('language.' . \Lang::get_lang() . '.' . $class, static::$_language);
		}
	}

	public function & __get($property)
	{
		if (in_array($property, static::$_lang_fields) && ! empty(static::$_language[$this->id][$property]))
		{
			return static::$_language[$this->id][$property];
		}

		return parent::get($property);
	}
}
