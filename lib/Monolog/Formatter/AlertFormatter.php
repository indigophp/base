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

use Monolog\Logger;

/**
 * Alert Handler
 *
 * Handle log messages in oil console
 *
 * @author Márk-Sági-Kazár <mark.sagikazar@gmail.com>
 */
class AlertFormatter implements FormatterInterface
{
	/**
	 * {@inheritdocs}
	 */
	public function format(array $record)
	{
		$from = \Arr::get($record, 'context.from');
		$to   = \Arr::get($record, 'context.to');

		return ucfirst(\Str::trans($record['message'], $from, $to));
	}

	/**
	 * {@inheritdocs}
	 */
	public function formatBatch(array $records)
	{
		$message = '';
		foreach ($records as $record) {
			$message .= $this->format($record);
		}

		return $message;
	}
}