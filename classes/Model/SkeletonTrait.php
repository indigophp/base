<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Model;

use Fuel\Fieldset\Form;
use Fuel\Fieldset\Builder\Basic;
use Fuel\Validation\Validator;
use Fuel\Validation\RuleProvider\FromArray;

/**
 * Skeleton Trait
 *
 * Use this trait to add additional features to your model
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait SkeletonTrait
{
	use \Fuel\Fieldset\Builder\ModelBuilder;
	use \Fuel\Validation\RuleProvider\ModelProvider;

	/**
	 * Extended properties
	 *
	 * @var array
	 */
	protected static $_columns_cached = array();

	/**
	 * Cached list properties
	 *
	 * @var array
	 */
	protected static $_list_cached = array();

	/**
	 * Cached form properties
	 *
	 * @var array
	 */
	protected static $_form_cached = array();

	/**
	 * Cached fieldsets
	 *
	 * @var array
	 */
	protected static $_fieldsets_cached = array();

	/**
	 * {@inheritdocs}
	 */
	public static function properties($columns = false)
	{
		$properties = parent::properties();
		$class = get_called_class();

		// If not already determined
		if ( ! array_key_exists($class, static::$_columns_cached))
		{
			static::$_columns_cached[$class] = $properties;

			foreach ($properties as $key => $value)
			{
				if (strpos($key, '.') !== false or \Arr::get($value, 'eav', false) !== false)
				{
					unset($properties[$key]);
				}
			}

			static::$_properties_cached[$class] = $properties;
		}

		// Return columns or properties
		if ($columns === true)
		{
			return static::$_columns_cached[$class];
		}
		else
		{
			return $properties;
		}
	}

	/**
	 * Fetches a column description array, or specific data from it
	 *
	 * @param   string  property or property.key
	 * @param   mixed   return value when key not present
	 * @return  mixed
	 */
	public static function column($key, $default = null)
	{
		$class = get_called_class();

		$properties = static::properties(true);

		return \Arr::get($properties, $key, $default);
	}

	/**
	 * Fetches properties displayed in view
	 *
	 * @return array
	 */
	public static function view()
	{
		$properties = static::properties(true);

		return array_filter($properties, function($item) {
			return \Arr::get($item, 'view', true) === true;
		});
	}

	/**
	 * Fetches properties displayed in list
	 *
	 * @return array
	 */
	public static function lists()
	{
		$class = get_called_class();

		// If already determined
		if (array_key_exists($class, static::$_list_cached))
		{
			return static::$_list_cached[$class];
		}

		$properties = static::properties(true);

		foreach ($properties as $key => &$property)
		{
			if (\Arr::get($property, 'list', false) === true)
			{
				$property['list'] = array();
			}
			elseif (\Arr::get($property, 'list.type', false) === false)
			{
				unset($properties[$key]);
				continue;
			}

			// Merge with form as defaults
			$property['list'] = \Arr::merge(
				\Arr::filter_keys(\Arr::get($property, 'form', array()), array('template'), true),
				\Arr::get($property, 'list')
			);
		}

		return static::$_list_cached[$class] = $properties;
	}

	/**
	 * Fetches properties displayed in form
	 *
	 * @return array
	 */
	public static function form($fieldset = false)
	{
		$class = get_called_class();

		// If already determined
		if (array_key_exists($class, static::$_form_cached))
		{
			return static::$_form_cached[$class];
		}

		$properties = static::properties(true);

		foreach ($properties as $key => $property)
		{
			if (\Arr::get($property, 'form.type', false) === false)
			{
				unset($properties[$key]);
				continue;
			}
		}

		return static::$_form_cached[$class] = $properties;
	}

	/**
	 * Get the class's fieldsets.
	 *
	 * @return array
	 */
	public static function fieldsets()
	{
		$class = get_called_class();
		$fieldsets = array();

		// If already determined
		if (array_key_exists($class, static::$_fieldsets_cached))
		{
			return static::$_fieldsets_cached[$class];
		}

		// Try to grab the properties from the class...
		if (property_exists($class, '_fieldsets'))
		{
			$fieldsets = static::$_fieldsets;
			foreach ($fieldsets as $fieldset => $legend)
			{
				if (is_int($fieldset))
				{
					unset($fieldsets[$fieldset]);
					$fieldsets[$legend] = $legend;
				}
			}
		}

		// cache the fieldsets for next usage
		static::$_fieldsets_cached[$class] = $fieldsets;

		return static::$_fieldsets_cached[$class];
	}

	/**
	 * {@inheritdocs}
	 */
	public static function populateValidator(Validator $validator)
	{
		$generator = new FromArray(true, 'validation');
		return $generator->setData(static::properties())->populateValidator($validator);
	}

	/**
	 * {@inheritdocs}
	 */
	public static function populateForm(Form $form)
	{
		if (static::$builder === null)
		{
			static::$builder = new Basic;
		}

		foreach (static::fieldsets() as $name => $fieldset)
		{
			if (is_array($fieldset) === false)
			{
				$fieldset = array('legend' => $fieldset);
			}

			$form[$name] = static::$builder->generateFieldset($fieldset);
		}

		// Loop through and add all the fields
		foreach (static::form() as $field => $config)
		{
			$instance = static::generateInput($field, $config);

			if ($fieldset = \Arr::get($config, 'form.fieldset', false) and isset($form[$fieldset]))
			{
				$form[$fieldset][$field] = $instance;
			}
			else
			{
				$form[$field] = $instance;
			}
		}

		return $form;
	}

	/**
	 * Processes the given field and add it to the form.
	 *
	 * @param  string $field          Name of the field to add
	 * @param  array  $propertyConfig Array of any config to be added to the field
	 * @param  string $prefix         Whether to generate input from form or list
	 * @return Input  Form input
	 */
	protected static function generateInput($field, $propertyConfig, $prefix = 'form')
	{
		// If type = false then do not add.
		$type = \Arr::get($propertyConfig, $prefix.'.type', 'text');

		if ($type === false)
		{
			return;
		}

		// Build up a config array to pass to the parent
		$config = array(
			'name'       => $field,
			'label'      => \Arr::get($propertyConfig, 'label', $field),
			'attributes' => \Arr::get($propertyConfig, $prefix.'.attributes', array()),
		);

		$content = \Arr::get($propertyConfig, $prefix.'.options', false);

		if ($content !== false)
		{
			foreach ($content as $value => $contentName)
			{
				if (is_array($contentName))
				{
					$group = array(
						'type'  => 'optgroup',
						'label' => $value,
					);

					foreach ($contentName as $optValue => $optName)
					{
						$group['content'][] = array(
							'type'    => 'option',
							'value'   => $optValue,
							'content' => $optName,
						);
					}

					$config['content'][] = $group;
				}
				else
				{
					$config['content'][] = array(
						'type'    => 'option',
						'value'   => $value,
						'content' => $field,
						'label'   => $contentName
					);
				}
			}
		}

		$instance = static::$builder->generateInput($type, $config);

		if ($template = \Arr::get($propertyConfig, $prefix.'.template', false))
		{
			$instance->setMeta('template', $template);
		}

		return $instance;
	}

	public static function generateFilters()
	{
		if (static::$builder === null)
		{
			static::$builder = new Basic;
		}

		$form = array();

		// Loop through and add all the fields
		foreach (static::lists() as $field => $config)
		{
			$form[] = static::generateInput($field, $config, 'list');
		}

		return $form;
	}
}
