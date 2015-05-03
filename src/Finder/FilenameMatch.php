<?php

namespace Finder;

class FilenameMatch
{
	/**
	 * @param string $pattern
	 * @return string
	 */
	public static function translate($pattern)
	{
		$i = 0;
		$n = mb_strlen($pattern);
		$result = '';
		while ($i < $n) {
			$c = $pattern[$i];
			++$i;
			if ($c === '*') {
				$result .= '.*';
			} elseif ($c === '?') {
				$result .= '.';
			} elseif ($c === '[') {
				$j = $i;
				if ($j < $n) {
					$c = $pattern[$j];
					if ($c === '!') {
						++$j;
					}
					if ($c === ']') {
						++$j;
					}
					while ($j < $n && $pattern[$j] !== ']') {
						++$j;
					}
					if ($j >= $n) {
						$result .= '\\[';
					} else {
						$stuff = str_replace('\\', '\\\\', substr($pattern, $i, $j));
						$i = $j + 1;
						if ($stuff[0] === '!') {
							$stuff = '^'.substr($stuff, 1);
						} elseif ($stuff[0] === '^') {
							$stuff = '\\'.$stuff;
						}
						$result .= '['.$stuff.']';
					}
				}
			} else {
				$result .= preg_quote($c, '/');
			}
		}
		return $result;
	}
}
