<?php defined('SYSPATH') OR die('No direct script access.');
class UTF8 extends Kohana_UTF8 {
 
	/**
	 * @var  boolean  Does the server support UTF-8 natively?
	 */
	public static $server_utf8 = NULL;
 
	/**
	 * @var  array  List of called methods that have had their required file included.
	 */
	public static $called = array();
 
	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 "equivalents".
	 *
	 *     $ascii = UTF8::transliterate_to_ascii($utf8);
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>	
	 * @param   string   $str
	 * @param   integer  $case
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
		if ( ! isset(self::$called[__FUNCTION__]))
		{
			require APPPATH.'classes/utf8'.DIRECTORY_SEPARATOR.__FUNCTION__.EXT;
			
			// Function has been called
			self::$called[__FUNCTION__] = TRUE;
		}
 
	return _Transliterate_to_ascii($str, $case);
	}
}
