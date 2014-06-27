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
 * Trans Formatter
 *
 * Translate messages
 *
 * @author Márk-Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TransFormatter implements FormatterInterface
{
	/**
	 * {@inheritdocs}
	 */
	public function format(array $record)
	{
		$from = \Arr::get($record, 'context.from');
		$to   = \Arr::get($record, 'context.to');

		if (empty($from))
		{
			return $record['message'];
		}
		elseif(is_array($from) === false)
		{
			$from = array($from => $to);
		}

		return strtr($record['message'], $from);
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