<?php
namespace App\Service;

use System\Core\Http\Request;
use Config\csrf;


class CSRF_Provider extends csrf
{
	private function validateMethod()
	{
		$request 	= new Request;
	
		$csrfMethod = $request->input('_csrf-method');
		$method	 	= empty($csrfMethod)? $_SERVER['REQUEST_METHOD'] : $csrfMethod;
		$rute 	 	= routeUrl();
		
		if(in_array($method, $this->method))
		{
			if($rute->method->origin == $method)
			{
				$_csrfToken = $request->input('_csrf-token');
				
				if(!empty($this->except[$method]))
				{
					if(in_array(routeUrlActive(), $this->except[$method]))
					{
						return 1;
					}
					else
					{
						if($_csrfToken == _getCsrf())
						{
							return 1;
						}
					}
				}
				else
				{
					if($_csrfToken == _getCsrf())
					{
						return 1;
					}
				}
			}
			
			\System\Core\ErrHandle\Error::errorExpired();
		}
	}
	
	public function register()
	{
		$this->validateMethod();
	}
}