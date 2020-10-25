<?php
namespace System\Core\Protection;

class csrf
{
	public static function run(Request $input)
	{
		if(self::except() == 0)
		{
			if(in_array($_SERVER['REQUEST_METHOD'],['POST','PUT','DELETE']))
			{
				if($input->isAjax() && isset($input->getHeader()['X-CSRF-TOKEN']))
				{
					/*if($input->getHeader()['X-CSRF-TOKEN'] != csrf())
					{
						 //self::setError();
					}*/
				}
				else
				{
					if($input->input('_csrf_token') != csrf())
					{
						self::setError();
					}
				}
			}
		}
		
	}
	
	public static function setError()
	{
		header('HTTP/1.1 419 Unknown status');
		\System\Core\ErrHandle\Error::errorExpired();
	}
	
	public static function getID()
	{
		$char 	= str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
		$tmp 	= hash('sha3-512', substr($char,5,16));
		
		$n  = substr($tmp,3,53);
		$n .= strtoupper(substr($tmp,30,70));
		
		return str_shuffle($n);
	}
	
	private static function except()
	{
		
		return 0;
	}
}