<?php
namespace System\Route;

class RouteModel_forNew
{
	private $controller = ['class' => '','method' => '', 'path' => ''];
	
	public function __construct(string $str = '')
	{
		if($str != '')
		{
			$explode = explode('@',$str);
			
			if(count($explode) == 2)
			{
				$obj = explode('/',$explode[0]);
				
				$class	= end($obj);
				$path	= str_replace($class,'',$explode[0]);
				$path	= preg_replace('/\/$/','',$path);
				
				$this->controller['path']   = $path;
				$this->controller['class']  = $class;
				$this->controller['method'] = $explode[1];
			}
		}
	}
	
	public function __get($name = '')
	{
		if($name != '')
		{
			if(isset($this->controller[$name]))
			{
				return $this->controller[$name];
			}
		}
	}
	
	public static function replaceSlash($str = '')
	{
		if($str != '')
		{
			return preg_replace('/\/$/','',$str);
		}
		return;
	}
}