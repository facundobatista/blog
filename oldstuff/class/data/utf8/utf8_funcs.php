<?php

/**
 * utf8 interrelated functions 
 * @autor CB
 * @email cb.utblog@gmail.com
 * @homepage http://www.utblog.com/plog/CB
 * @date 25 Jul 2005
 */

/**
 * int utf8_isValidChar(string $inputStr, $start = 0)
 * Is it a valid utf8 character
 * @param $inputStr input string
 * @param $start start index
 * @return the ascii bytes of the utf8 char if it is a valid utf8 char. 0 if input array is empty, or -1 if it's invalid 
 * @note don't use pass-by-reference for $inArr here, otherwise efficiency will decreased significantly 
 * @note change param $inArr from char array to string ($inputStr), for porformance purpose.
 * @note preg_split consumes too much memory and cpu when split a big string to char array
 */
function utf8_isValidChar($inputStr, $start = 0)
{
	$size = strlen($inputStr);
	if($size <=0 || $start < 0 || $size <= $start) return 0;

	$inOrd = ord($inputStr{$start});
	$us = 0;
	if($inOrd <= 0x7F) { //0xxxxxxx
		return 1;
	} else if($inOrd >= 0xC0 && $inOrd <= 0xDF ) { //110xxxxx 10xxxxxx
		$us = 2;
	} else if($inOrd >= 0xE0 && $inOrd <= 0xEF ) { //1110xxxx 10xxxxxx 10xxxxxx
		$us = 3;
	} else if($inOrd >= 0xF0 && $inOrd <= 0xF7 ) { //11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
		$us = 4;
	} else if($inOrd >= 0xF8 && $inOrd <= 0xFB ) { //111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
		$us = 5;
	} else if($inOrd >= 0xFC && $inOrd <= 0xFD ) { //1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
		$us = 6;
	} else
		return -1;

	if($size - $start < $us)
		return -1;

	for($i=1; $i<$us; $i++)
	{
		$od = ord($inputStr{$start+$i}); 
		if($od <0x80 || $od > 0xBF)
			return -1;
	}
	return $us;
}

/**
 * mix utf8_substr(string $inputString, int $start_index, int $length = -1, bool $ignore_invalid_utf8_char = true)
 * @param $inputStr
 * @param $start start index, must be large than 0
 * @param $length. if $length <0, return all text begin from $start
 * @param $ignore_error whether ignore the invalid characters (in return string, these invalid chars will be replaced with '?') or not. default is true (ignore)
 * @return the substring, or false (empty string '')
 */
function utf8_substr($inputStr, $start, $length = -1, $ignore_error = true)
{
	if($start<0 || $length == 0)
		return false;
	//discard preg_split function. it consumes too much system resource when it tries to split a big string to pieces
	//$rawArr = preg_split('//',$inputStr,-1, PREG_SPLIT_NO_EMPTY); 
	//find start
	$si = 0;
	$si_single = 0;
	while($si < $start)
	{
		$hm = utf8_isValidChar($inputStr, $si_single);
		if($hm == -1)
		{
			//ignore invalid character?
			if(!$ignore_error)
				return false;
			//array_shift is very slow
			//array_shift($rawArr); 
			$si++;
			$si_single++;
		}
		else if($hm == 0)
		{
			//$start is bigger than the utf8_length of inputString
			return false;
		}
		else
		{
			//for($i=0; $i<$hm; $i++) array_shift($rawArr);
			$si++;
			$si_single += $hm;
		}
	}
	if($length < 0)
		//return implode('', $rawArr);
		return substr($inputStr, $si_single);
	$retArr = array();
	$li = 0;
	while($li < $length)
	{
		$hm = utf8_isValidChar($inputStr, $si_single);
		if($hm == -1)
		{
			if(!$ignore_error)
				return false;
			$retArr[] = '?'; 
			//array_shift($rawArr);
			$li++;
			$si_single++;
		}
		else if($hm == 0)
		{
			//end of string
			return implode('', $retArr);
		}
		else
		{
			//for($i=0; $i<$hm; $i++) $retArr[] = array_shift($rawArr);
			for($i=0; $i<$hm; $i++) $retArr[] = $inputStr{$si_single++};
			$li++;
		}
	}
	return implode('', $retArr);
}

/**
 * int utf8_strlen(string $inputString, bool $ignore_invalid_utf8_char = true)
 * @return length of string encoded as utf8 ( how many utf8 characters )
 * -1 if given $ignore_error is false and there's invalid utf8 char in the inputString
 * @note if $ignore_error is true (the default value), every invalid utf8 character will be count as ONE utf8 char
 */
function utf8_strlen($inputStr, $ignore_error = true)
{
	//$rawArr = preg_split('//',$inputStr,-1, PREG_SPLIT_NO_EMPTY); 
	$len = 0;
	$si_single = 0;
	while(($hm = utf8_isValidChar($inputStr, $si_single)) != 0)
	{
		if($hm == -1)
		{
			if(!$ignore_error)
				return -1;
			//array_shift($rawArr);
			$si_single++;
		}
		else
			//for($i=0; $i<$hm; $i++) array_shift($rawArr);
			$si_single += $hm;
		$len++;
	}
	return $len;
}

/**
 * int utf8_proportion(string $inputString)
 * @param $inputString
 * @return percentage of valid utf8 chars of $inputString
 * @see http://www.utblog.com/plog/1/article/292
 */ 
function utf8_proportion($inputStr)
{
	//$rawArr = preg_split('//',$inputStr,-1, PREG_SPLIT_NO_EMPTY); 
	//$rawLen = count($rawArr);
	$rawLen = strlen($inputStr);
	if($rawLen == 0)
		return 100;
	$validChars = 0;
	$si_single = 0;
	while(($hm = utf8_isValidChar($inputStr, $si_single)) != 0)
	{
		if($hm == -1)
		{
			//array_shift($rawArr);
			$si_single++;
			continue;
		}
		//for($i=0; $i<$hm; $i++) array_shift($rawArr);
		$validChars += $hm;
		$si_single += $hm;
	}
	if($validChars == $rawLen)
		return 100;
	else
		return (int)($validChars*100.0/$rawLen);
}

?>
