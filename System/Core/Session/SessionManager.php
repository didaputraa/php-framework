<?php
namespace System\Core\Session;

use System\Core\Session\SessionHandleManager as Manager;
use System\Core\Session\SessionDestroy;
use System\Core\Session\Session;
use System\Core\Protection\csrf;
use Config\App;

class SessionManager
{
	public function setUP($file)
	{
		$app = App::getSession();
		
		session_save_path($file);
		session_set_save_handler(new Manager, true);
		session_start([
			'name'				=> $app->name,
			'cookie_lifetime'	=> $app->lifetime,
			'cookie_domain'		=> $app->domain,
			'cookie_httponly'	=> $app->httpOnly,
			'cookie_secure'		=> $app->secure,
			'cookie_path'		=> '/'
		]);
	}
	
	public static function run($sess)
	{
		if(Session::get('__GC-SESSION-CHECK__') == '')
		{
			Session::set('__GC-SESSION-CHECK__', 0);
		}

		if(in_array(date('i', strtotime('now')),[0, 5, 10, 15, 20, 25, 30,35,40,45,50,55]))
		{
			$gc = Session::get('__GC-SESSION-CHECK__');
			
			if($gc < 2)
			{
				SessionDestroy::execute($sess);
				Session::set('__GC-SESSION-CHECK__', (int)$gc+1);
			}
		}
		else
		{
			Session::set('__GC-SESSION-CHECK__', 0);
		}
		
		if(Session::get('__lookupIP__') == '')
		{
			Session::set('__lookupIP__', $_SERVER['REMOTE_ADDR']);
		}
		
		if(Session::get('__CSRF-TOKEN__') == '')
		{
			Session::set('__CSRF-TOKEN__', csrf::getID());
		}
		
		if(Session::get('__CSRF-TIME__') == '')
		{
			Session::set('__CSRF-TIME__', time() + App::getProtection()->csrf);
		}
		else
		{
			if(Session::get('__CSRF-TIME__') < time())
			{
				Session::set('__CSRF-TOKEN__', csrf::getID());
				Session::set('__CSRF-TIME__', time() + App::getProtection()->csrf);
			}
		}
	}
}