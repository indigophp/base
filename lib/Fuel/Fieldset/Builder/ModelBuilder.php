<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuel\Fieldset\Builder;

use Fuel\Fieldset\Builder\V1Model;
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
trait ModelBuilder
{
	protected static $builder;

	/**
	 * Forge a new Form instance
	 *
	 * @return Form
	 */
	public static function forgeForm()
	{
		$form = new Form;

		return static::populateForm($form);
	}

	/**
	 * Should populate the given form with the needed fields.
	 *
	 * @param  Form $form
	 * @return Form
	 */
	public static function populateForm(Form $form)
	{
		if (static::$builder === null)
		{
			static::$builder = new V1Model;
			static::$builder->setWrapperElement(null);
		}

		$elements = static::$builder->generate(get_called_class());

		return $form->setContents($elements);
	}
}
