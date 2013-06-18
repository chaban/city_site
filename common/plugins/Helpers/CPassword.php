<?php namespace Helpers;
class CPassword {
	
	public static function generate($count)
	{
		$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','y','z','1','2','3','4','5','6','7','8','9','0');
		$result = '';
		for($i = 0; $i < $count; $i++)
		{
			$result .= $chars[rand(0, count($chars)-1)];
		}
		return $result;
	}
}