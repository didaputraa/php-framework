<?php
namespace System\Core;

use \Config\App;

class Auth
{
	private static $instance = null;
	
	private function return()
	{
		if(self::$instance == null)
		{
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	public static function permission()
	{
		
	}
	
	public static function detail()
	{
		return $_SESSION['__AUTH-USER__'];
	}
	
	public static function attempt($fields = [])
	{
		$pass 		= '';
		$pass_check = 1;
		
		if(!empty($fields))
		{
			$table	= App::getGuard()->model;
			
			$class = new $table;
			
			foreach($fields as $field => $value)
			{
				if($field != 'password')
				{
					$class->where($field, htmlspecialchars($value ,ENT_QUOTES));
				}
				else
				{
					$pass = htmlspecialchars($value, ENT_QUOTES);
				}
			}
			
			if(!empty($pass))
			{
				if($class->count() > 0)
				{
					$password = $class->first();
				
					if(password_verify($pass, $password->password))
					{
						$class->where('password', $password->password);
					}
					else
					{
						$pass_check = 0;
					}
				}
				else
				{
					$pass_check = 0;
				}
			}
			
			if($class->count() == 1 && $pass_check == 1)
			{
				$_SESSION['__AUTH-STATUS__']= 1;
				$_SESSION['__AUTH-USER__']	= $class->first();
				
				return true;
			}
		}
		
		return false;
	}
	
	private function password($value = '')
	{
		return password_hash($value, PASSWORD_BCRYPT);
	}
	
	public static function check()
	{
		if(!empty($_SESSION['__AUTH-STATUS__']) && !empty($_SESSION['__AUTH-USER__']))
		{
			return 1;
		}
		
		return 0;
	}
	
	public static function logout()
	{
		unset($_SESSION['__AUTH-STATUS__'], $_SESSION['__AUTH-USER__']);
	}
}
