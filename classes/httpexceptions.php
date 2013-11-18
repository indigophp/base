<?php

namespace Indigo\Base;

class HttpForbiddenException extends \HttpException
{
	public function response()
	{
		return new \Response(\View::forge('403'), 403);
	}
}

class HttpUnauthorizedException extends \HttpException
{
	public function response()
	{
		return new \Response(\View::forge('401'), 401);
	}
}