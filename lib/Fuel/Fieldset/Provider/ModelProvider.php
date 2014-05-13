<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuel\Fieldset\Provider;

use Fuel\Fieldset\Form;
use Fuel\Fieldset\Fieldset;
use Fuel\Fieldset\Input\Optgroup;
use Fuel\Fieldset\Input\Option;
use Fuel\Common\Arr;
use InvalidArgumentException;

/**
 * Allows sets of form fields to be generated from an ORM Model source
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait ModelProvider
{
	/**
	 * Cached fieldsets
	 *
	 * @var array
	 */
	protected static $_fieldsets_cached = array();

	/**
	 * Should populate the given form with the needed fields.
	 *
	 * @param  Form $form
	 * @return Form
	 */
	public static function populateForm(Form $form)
	{
		// Loop through and add all the fieldsets
		foreach (static::fieldsets() as $fieldset => $legend)
		{
			static::addFieldset($fieldset, $legend, $form);
		}

		// Loop through and add all the fields
		foreach (static::properties() as $field => $attributes)
		{
			static::addField($field, $attributes, $form);
		}

		return $form;
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
	 * Processes the given field and add it to the form.
	 *
	 * @param string $field      Name of the field to add
	 * @param array  $attributes Array of any attributes to be added to the field
	 * @param Form   $form       Form object to add fields to
	 */
	protected static function addField($field, $attributes, Form $form)
	{
		if (($type = Arr::get($attributes, 'form.type', false)) === false)
		{
			return;
		}

		$class = 'Fuel\\Fieldset\\Input\\' . ucfirst($type);
		$label = Arr::get($attributes, 'label');
		$attr = Arr::get($attributes, 'form.attributes', array());
		$default = Arr::get($attributes, 'default');
		$validation = Arr::get($attributes, 'validation', array());

		if ($type == 'select')
		{
			$element = new $class;
			$element->setName($field);
			$element->setAttributes($attr);

			if ($options = Arr::get($attributes, 'form.options', false))
			{
				foreach ($options as $option => $value)
				{
					if (is_array($value))
					{
						$option = array(
							'_content' => $value,
						);

						$element[] = Optgroup::fromArray($option);
						continue;
					}

					$element[] = new Option($value, $option);
				}
			}

			// $element->setValue($default);
		}
		else
		{
			$element = new $class($field, $attr, $default);
		}

		$element->setLabel($label);
		$element->setMeta('validation', $validation);

		if ($fieldset = Arr::get($attributes, 'form.fieldset') and isset($form[$fieldset]))
		{
			$form[$fieldset][$field] = $element;

			return;
		}

		$form[$field] = $element;
	}

	/**
	 * Processes the given fieldset and add it to the form.
	 *
	 * @param string $fieldset   Name of the fieldset to add
	 * @param string $legend
	 * @param Form   $form       Form object to add fields to
	 */
	protected static function addFieldset($fieldset, $legend, Form $form)
	{
		$form[$fieldset] = new Fieldset;
		$form[$fieldset]->setLegend($legend);
	}
}
