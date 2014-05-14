<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base;

/**
 * HTTP Forbidden Exception
 *
 * Return 403 code
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class HttpForbiddenException extends \HttpException
{
	public function response()
	{
		return new \Response(\View::forge('403'), 403);
	}
}
