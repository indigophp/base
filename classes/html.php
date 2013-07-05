<?php

class Html extends \Fuel\Core\Html
{
	public static function br($num = 1)
	{
		is_int($num) || $num = (int) $num;

		$result = "";

		for ($i=0; $i < $num; $i++)
		{
			$result .= '<br />';
		}

		return $result;
	}
}