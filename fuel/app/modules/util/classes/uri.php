<?php

namespace Util;

class Uri
{
	static function params()
	{
		$query = \Uri::build_query_string(\Input::get());
	
		if (strlen($query) > 0)
			return  '?' . $query;
		else
			return '';
	}
}