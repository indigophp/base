<?php

/*
 * This file is part of the Indigo Core package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\AlertFormatter;

/**
 * Alert Handler
 *
 * Handle log messages in oil console
 *
 * @author Márk-Sági-Kazár <mark.sagikazar@gmail.com>
 */
class AlertHandler extends AbstractProcessingHandler
{
	/**
	 * {@inheritdocs}
	 */
	public function __construct($level = Logger::INFO, $bubble = true)
	{
		parent::__construct($level, $bubble);
	}

	/**
	 * {@inheritdocs}
	 */
	protected function write(array $record)
	{
		\Session::set_flash('alert.'.$this->getTemplate($record), $record);
	}

	/**
	 * Get template name
	 *
	 * @param  array  $record
	 * @return string
	 */
	protected function getTemplate(array $record)
	{
		$template = \Arr::get($record, 'context.template', $record['level_name']);

		return strtolower($template);
	}

	/**
	 * {@inheritdocs}
	 */
    protected function getDefaultFormatter()
    {
        return new AlertFormatter();
    }
}