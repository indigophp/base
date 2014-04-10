<?php

namespace Auth;

trait Controller_Base
{
	public function before($data = null)
	{
		parent::before($data);

		if ( ! \Auth::check())
		{
			\Response::redirect('/auth/login?uri=' . urlencode(\Uri::string()));
		}
	}
}
