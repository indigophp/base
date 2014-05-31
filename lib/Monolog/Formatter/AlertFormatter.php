<?php

/*
 * This file is part of the Indigo Core package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

/**
 * Alert Formatter
 *
 * Format alert messages
 *
 * @author Márk-Sági-Kazár <mark.sagikazar@gmail.com>
 */
class AlertFormatter extends TransFormatter
{
	/**
	 * {@inheritdocs}
	 */
	public function format(array $record)
	{
		return ucfirst(parent::format($record));
	}
}