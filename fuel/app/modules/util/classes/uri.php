<?php

namespace Util;

class Uri
{
	static function filter_params()
	{
		$input = \Input::get();
		$output = array();
		foreach ($input as $key => $value) 
		{
			if (strlen($value) > 0) 
			{
				$output[$key] = $value;
			}
		}

		return $output;
	}

	static function params()
	{
		$query = \Uri::build_query_string(\Util\Uri::filter_params());

		if (strlen($query) > 0)
			return  '?' . $query;
		else
			return '';
	}
}
