<?php
namespace System\Core\Protection;

use \System\Route\RouteManager as route;
use \Config\Kernel;

class ServiceProvider
{
	private $kernel = null;
	

	public function __construct()
	{
		$this->kernel = new Kernel;
	}
	
	public function invokeProvider()
	{
		foreach($this->kernel->serviceRoute as $services => $classes)
		{
			if(file_exists($classes.'.php'))
			{
				$svc = new $classes;
				
				$svc->register();
			}
		}
	}
	
	public function invokeMiddleware($object = [])
	{
		$rute = $object;
		
		if(empty($rute->classes->middleware))
		{
			return 1;
		}
		else
		{
			foreach($rute->classes->middleware as $thread_name)
			{
				if(isset($this->kernel->middlewareRoute[$thread_name]))
				{
					$class = $this->kernel->middlewareRoute[$thread_name];
					
					$sc = new $class;
					$sc->handle(new \System\Core\Http\Request);
				}
				else
				{
					$debug  = debug_backtrace();
					$msg	= 'Middleware '.$thread_name.' not found';
					
					\System\Core\ErrHandle\Error::errorCode(0, $msg, $debug[0]['args'][0]->classes->class, 0);
					
					break;
				}
			}
		}
		
	}
}