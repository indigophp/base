<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extension;

use Fuel\Fieldset\Element;
use Twig_Extension;

class Fieldset extends Twig_Extension
{
	/**
	 * {@inheritdocs}
	 */
	public function getName()
	{
		return 'fieldset';
	}

	/**
	 * {@inheritdocs}
	 */
	public function getFunctions()
	{
		return array(
			'getFormElementType' => new \Twig_Function_Method($this, 'getFormElementType'),
		);
	}

	/**
	 * {@inheritdocs}
	 */
	public function getFilters()
	{
		return array(
			'attr' => new \Twig_Filter_Method($this, 'arrayToAttr'),
		);
	}

	/**
	 * {@inheritdocs}
	 */
	public function getTests()
	{
		return array(
			'fieldset' => new \Twig_Test_Method($this, 'isFieldset')
		);
	}

	/**
	 * Check whether given object is instance of Fieldset
	 *
	 * @param  mixed  $fieldset
	 * @return boolean
	 */
	public function isFieldset($fieldset)
	{
		return $fieldset instanceof \Fuel\Fieldset\Fieldset;
	}

	/*
	No general interface for now
	 */
	public function getFormElementType($element)
	{
		$type = $element->getMeta('template', false);

		if ($type === false)
		{
			$type = strtolower(\Inflector::denamespace(get_class($element)));
		}

		return $type;
	}

	public function arrayToAttr($array, array $keys = array(), $delete = false)
	{
		$array = (array) $array;
		$array = \Arr::filter_keys($array, $keys, $delete);

		return array_to_attr($array);
	}
}