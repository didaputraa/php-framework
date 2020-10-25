<?php
namespace System\Core\Session;

class Session
{
	public static function set($key = '', $val = '')
	{
		$_SESSION[$key] = $val;
	}
	
	public static function get($key = '')
	{
		return $sess = empty($_SESSION[$key])? '' : $_SESSION[$key];
	}
	
	public static function destroy($key = '')
	{
		unset($_SESSION[$key]);
	}
}