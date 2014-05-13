<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuel\Validation\RuleProvider;

use Fuel\Validation\Validator;
use Fuel\Validation\RuleProvider\FromArray;

/**
 * Allows sets of validation rules to be generated from an ORM Model source
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait ModelProvider
{
	/**
	 * Should populate the given validator with the needed rules.
	 *
	 * @param  Validator $validator
	 * @return Validator
	 */
	public static function populateValidator(Validator $validator)
	{
		$generator = new FromArray(true, 'validation');
		return $generator->setData(static::properties())->populateValidator($validator);
	}
}
