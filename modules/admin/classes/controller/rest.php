<?php

namespace Admin;

class Controller_Rest extends \Controller_Rest
{
	public function before($data = null)
	{
		parent::before($data);
		if (\Auth::check())
		{
			if ( ! \Auth::has_access('admin.view'))
			{
				throw new \HttpForbiddenException();
			}
		}
		else
		{
			throw new \HttpUnauthorizedException();
		}
	}
}
