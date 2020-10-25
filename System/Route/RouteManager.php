<?php
namespace System\Route;

use System\Route\RouteModel;

class RouteManager extends RouteModel
{
	public static $regexRoute 	= '.+?';
	
	private static $routes 		= ['name' => '', 'active' => '', 'path' => ''];
	
	
	public static function post($url, $controller)
	{
		self::methods($url, $controller, 'POST');
		
		return self::return();
	}
	
	public static function delete($url, $controller)
	{
		self::methods($url, $controller, 'DELETE');
		
		return self::return();
	}
	
	public static function put($url, $controller)
	{
		self::methods($url, $controller, 'PUT');
		
		return self::return();
	}
	
	public static function get($url, $controller)
	{
		self::methods($url, $controller);
		
		return self::return();
	}
	
	public function group($a = null, $b = null)
	{
		if(gettype($a) == 'object')
		{
			$a();
			
			return;
		}
		elseif(gettype($a) == 'array')
		{
			self::$route['group'] = $a;
			
			if(gettype($b) == 'object')
			{
				$b();
				
				self::$route['group'] = [];
				
				return;
			}
		}
	}
	
	public function build()
	{
		self::generateRoute();
	}
	
	private function activePointer()
	{
		$key = array_column(self::$RouteMap['map'][self::$route['methodActive']], 'url');
		$pos = array_search(self::$route['urlActive'], $key);
		
		return $pos;
	}
	
	public function name($name = '')
	{
		if($name != '')
		{
			if(isset(self::$route['methodActive']) && isset(self::$route['urlActive']))
			{
				if(isset(self::$RouteMap['map'][self::$route['methodActive']]))
				{
					$names 	= '';
					$key 	= array_column(self::$RouteMap['map'][self::$route['methodActive']], 'url');
					$pos 	= array_search(self::$route['urlActive'], $key);
					
					if(isset(self::$route['group']['name']))
					{
						$names .= self::$route['group']['name'].$name;
					}
					else
					{
						$names .= $name;
					}
					
					self::$RouteMap['map'][self::$route['methodActive']][$pos]['name'] = $names;
				}
			}
		}
		
		return self::return();
	}
	
	public function middleware($listName = [])
	{
		if(!empty($listName))
		{
			if(isset(self::$route['methodActive']) && isset(self::$route['urlActive']))
			{
				if(isset(self::$RouteMap['map'][self::$route['methodActive']]))
				{
					$pointer = self::activePointer();
					$mdr 	 = [];
					$param   = gettype($listName) == 'array'? $listName : [0 => $listName];
					
					if(isset(self::$route['group']['middleware']))
					{
						$group = self::$route['group']['middleware'];
						
							$mdr = gettype($group)=='array'? 
								[...$group, ...array_values($param)] :
								[$group, ...array_values($param)];
					}
					else
					{
						$mdr = [...$param];
					}
					
					self::$RouteMap['map'][self::$route['methodActive']][$pointer]['middleware'] = $mdr;
				}
			}
		}
		
		return self::return();
	}
	
	public function getRoutes()
	{
		return self::$route['route'];
	}
}