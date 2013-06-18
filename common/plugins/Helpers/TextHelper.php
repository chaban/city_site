<?php
namespace Helpers;
class TextHelper
{
	static public function Translit($text)
	{
		$translit = array(
			"А" => "a",
			"Б" => "b",
			"В" => "v",
			"Г" => "g",
			"Д" => "d",
			"Е" => "e",
			"Ё" => "e",
			"Ж" => "zh",
			"З" => "z",
			"И" => "i",
			"Й" => "i",
			"К" => "k",
			"Л" => "l",
			"М" => "m",
			"Н" => "n",
			"О" => "o",
			"П" => "p",
			"Р" => "r",
			"С" => "s",
			"Т" => "t",
			"У" => "u",
			"Ф" => "f",
			"Х" => "h",
			"Ц" => "c",
			"Ч" => "ch",
			"Ш" => "sh",
			"Щ" => "sch",
			"Ъ" => "",
			"Ы" => "y",
			"Ь" => "",
			"Э" => "e",
			"Ю" => "yu",
			"Я" => "ya",
			"а" => "a",
			"б" => "b",
			"в" => "v",
			"г" => "g",
			"д" => "d",
			"е" => "e",
			"ё" => "e",
			"ж" => "zh",
			"з" => "z",
			"и" => "i",
			"й" => "i",
			"к" => "k",
			"л" => "l",
			"м" => "m",
			"н" => "n",
			"о" => "o",
			"п" => "p",
			"р" => "r",
			"с" => "s",
			"т" => "t",
			"у" => "u",
			"ф" => "f",
			"х" => "h",
			"ц" => "c",
			"ч" => "ch",
			"ш" => "sh",
			"щ" => "sch",
			"ъ" => "",
			"ы" => "y",
			"ь" => "",
			"э" => "e",
			"ю" => "yu",
			"я" => "ya",
			'-' => '-',
			'_' => '-',
			'--' => '-',
			'---' => '-',
			'----' => '-',
			'?' => '',
			'.' => '',
			'"' => '',
			'&quot;' => '',
			' ' => '-');
		$tire = array(
			'--' => '-',
			'---' => '-',
			'----' => '-');
		$text = trim($text);
		$text = preG_replace('#\s{2,}#', ' ', $text);
		$text = strtr($text, $translit);
		$text = preg_replace('#[^0-9a-zA-Z\-]#', '', $text);
		$text = strtr($text, $tire);
		$text = preg_replace('#\-{2,}#', '-', $text);
		return $text;
	}
	/**
	 * Get money in specific format 1234 = 1 234
	 * @static
	 * @param $price money number
	 * @return string money in specific format
	 */
	public static function GetMoneyFormat($price)
	{
		$price = (string )$price;
		$res = '';
		for ($i = strlen($price) - 1; $i >= 0; $i--)
		{
			$res .= $price{$i};
			if (((strlen($price) - $i) % 3) == 0)
				$res .= ' ';
		}
		return strrev($res);
	}

	public static function GetFormatWord($word, $number)
	{
		$num = $number % 10;
		if ($word == 'оценка')
		{
			if ($num == 1)
				return 'оценка';
			elseif ($num > 1 && $num < 5)
				return 'оценки';
			else
				return 'оценок';
		}
	}
  
  public static function string2array($tags)
	{
		return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
	}

	public static function array2string($tags)
	{
		return implode(', ', $tags);
	}
  
}
?>