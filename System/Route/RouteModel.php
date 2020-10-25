<?php
namespace System\Route;

use System\Route\RouteModel_forNew as MRes;
use System\Core\Http\Request as Url;

abstract class RouteModel
{
	public static $regexRoute;
	
	protected static $route = [
		'name' 			=> ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []], 
		'urlActive' 	=> '', 
		'methodActive' 	=> '', 
		'group' 		=> [],
		'route' 		=> [], 
		'path' 			=> 'App/Controller/'
	];
	
	private static $instance= 0;
	
	private static $path 	= 'Storage/cacheSystem';
	
	protected static $RouteMap = [
		'map' 				=> [
			'GET' 			=> [],
			'GET_PARAM'		=> [],
			'POST'  		=> [],
			'POST_PARAM'  	=> [],
			'PUT'  			=> [],
			'PUT_PARAM'  	=> [],
			'DELETE'  		=> [],
			'DELETE_PARAM'  => [],
		]
	];
	
	
	protected function return()
	{
		if(empty(self::$instance))
		{
			self::$instance = new static;
		}
		
		return self::$instance;
	}
	
	final protected static function methods($url = '', $controller = '', $method = 'GET')
	{
		$jenis = (preg_match('/\{\w+\}/', $url, $res))? 'param' : 'default';
		
		self::pushRouteMap($url, $controller, $method, $jenis);
	}
	
	final protected static function pushRouteMap($url, $controller, $method, $type = 'default')
	{
		$middleware = [];
		if(isset(self::$route['group']['middleware']))
		{
			$mdr = self::$route['group']['middleware'];
			
			if(gettype($mdr) == 'array')
			{
				$middleware = [...self::$route['group']['middleware']];
			}
			elseif(gettype($mdr) == 'string' && $mdr != '')
			{
				$middleware = [self::$route['group']['middleware']];
			}
		}
		
		$opt = [
			'name'		=> '',
			'controller'=> $controller,
			'middleware'=> $middleware,
			'url'		=> $url,
		];
		
		self::$route['urlActive'] = $url;
		
		switch($type)
		{
			case 'default':
				
				self::$route['methodActive']  = $method;
				
				self::pushRoutetoMap($method, $opt);
				
			break;
			
			case 'param':
				
				self::$route['methodActive']  = $method.'_PARAM';
				
				$param = array_slice(explode('/',$url),1);
				$tmp   = [];
				$tmpUrl= [];
				
				foreach($param as $number => $i)
				{
					if($i != '')
					{
						if(preg_match('/\{\w+\}/',$i,$r))
						{
							$tmp[$number] = $i;
							$tmpUrl[]	  = '('.static::$regexRoute.')';
						}
						else
						{
							$tmpUrl[] = $i;
						}
					}
				}
				
				$opt['param'] 	  = $tmp;
				$opt['url_param'] = implode($tmpUrl, '\/');
				
				self::pushRoutetoMap($method.'_PARAM', $opt);
				
			break;
		}
	}
	
	final private static function pushRoutetoMap($type, $value)
	{
		self::$RouteMap['map'][$type][] = $value;
	}
	
	final private function buildMiddlewares($val)
	{
		$middleware = "[";
		
		if(count($val['middleware']) > 0)
		{
			foreach($val['middleware'] as $num => $name)
			{
				$middleware .= "{$num} => '{$name}', ";
			}
			
			$middleware = substr($middleware,0,-2);
		}
		
		return $middleware .= ']';
	}
	
	final private function writeToPHP($method, $val = [], $no, $type = 0)
	{
		if($type == 0)
		{
			$middleware = self::buildMiddlewares($val);
			
			return "\t\t{$no} => [
			'name' 		 => '{$val['name']}',
			'url' 		 => '{$val['url']}',
			'controller' => '{$val['controller']}',
			'middleware' => {$middleware}\n\t\t],\n";
		}
		else
		{
			$middleware = self::buildMiddlewares($val);
			
			$param 		= '[';
			foreach($val['param'] as $int => $n)
			{
				$param .= "{$int} => '".str_replace(['{','}'],'',$n)."', ";
			}
			$param .= ']';
			
			return "\t\t{$no} => [
			'name'			=> '{$val['name']}',
			'url' 			=> '{$val['url']}',
			'controller' 	=> '{$val['controller']}',
			'middleware'	=> {$middleware},
			'url_param' 	=> '{$val['url_param']}',
			'param' 		=> {$param}\n\t\t],\n";
		}
	}
	
	/*
		update cache file route
	*/
	function fileCacheSystem()
	{
		$path 	 = self::$path;
		$filename= '/';
		$dir 	 = array_slice(scandir($path),2);
		$times 	 = filemtime('Route/url.php');
		
		foreach($dir as $name)
		{
			if(preg_match('/^(cacheSystem_)([0-9]+).php$/', $name, $cache))
			{
				if($times > $cache[2])
				{
					unlink($path.'/'.$cache[1].$cache[2].'.php');
					
					$filename .= $cache[1].$times.'.php';
				}
				else
				{
					$filename .= $cache[1].$cache[2].'.php';
				}
				break;
			}
		}
		
		if($filename == '/')
		{
			$filename .= "cacheSystem_{$times}.php";
		}
		
		self::$route['cache_system'] = $filename;
		
		return $filename;
	}
	
	/*
		generate rute
	*/
	protected function generateRoute()
	{
		$path 	  = self::$path;
		$filename = self::$route['cache_system'];
		
		if(!file_exists($path.$filename))
		{
			$create = fopen($path.$filename,'w');
			fclose($create);
		}
		
		$writeArrayPhp = function($method, $vals)
		{
			$param 	= 0;
			$no  	= 0;
			$str 	= "\t'{$method}' => [\n";
			$param  = preg_match('/\w+_PARAM/',$method)? 1 : 0;
			
			foreach($vals as $val)
			{
				$str .= self::writeToPHP($method,$val, $no, $param);
				$no++;
			}
			
			return $str .= "\t],\n";
		};
		
		$open = fopen($path.$filename,'a');
		fwrite($open, "<?php\nreturn [\n");
		$str_ = '';
		
		foreach(self::$RouteMap['map'] as $k => $v)
		{
			$str_ .= $writeArrayPhp($k, $v);
		}
		
		fwrite($open,$str_."];\n");
		fclose($open);
		
		self::$RouteMap 		= [];
		self::$route['name'] 	= [];
	}
	
	/*
		cek kosong / perlu update
	*/
	public static function checkEmptyOrUpdate()
	{
		$file = self::fileCacheSystem();
		
		if(file_exists(self::$path.$file))
		{
			return 0;
		}
		
		return 1;
	}
	
	public function routeExists()
	{
		$map 		= require(self::$path.self::$route['cache_system']);
		$checking	= 0;
		
		$request 	= new Url;
		
		$csrfMethod = $request->input('_csrf-method');
		
		$method		= empty($csrfMethod)? $_SERVER['REQUEST_METHOD'] : $csrfMethod;
		
		
		if(isset($method) && in_array($method,['GET', 'POST', 'PUT', 'DELETE']))
		{
			$uri = $request->UrlRender();
			
			self::$route['route']['classes']['fullPath'] = self::$route['path'];
			
			foreach($map[$method] as $url)
			{
				if($uri == $request->UrlRender($url['url']))
				{
					self::$route['route']['urlActive'] 	= $url['url'];
					self::$route['route']['url'] 		= $uri;
					self::$route['route']['method'] 	= (object)['origin' => $method, 'render' => $method];
					
					$app = new MRes($url['controller']);
						
					self::$route['route']['classes']['controller'] 	= $url['controller'];
					self::$route['route']['classes']['middleware']	= empty($url['middleware'])? '' : $url['middleware'];
					self::$route['route']['classes']['class'] 		= $app->class;
					self::$route['route']['classes']['method'] 		= $app->method;
					self::$route['route']['classes']['path'] 		= $app->path;
					self::$route['route']['classes']['fullPath']   .= $app->path;
					
					self::$route['route']['classes'] = (object)self::$route['route']['classes'];
					self::$route['route'] 			 = (object)self::$route['route'];
					
					$checking = 1;
					break;
				}
			}
			
			if($checking == 0)
			{
				$method = $method.'_PARAM';
				
				foreach($map[$method] as $url)
				{
					if(preg_match('/^'.$url['url_param'].'$/', $uri, $matchs))
					{
						$match = array_slice($matchs,1);
						$tmp   = [];
						$tmpUrl= explode('/', $request->UrlRender());
						
						self::$route['route']['urlActive']= $url['url'];
						self::$route['route']['url'] 	  = $uri;
						self::$route['route']['method']   = (object)['origin' => $_SERVER['REQUEST_METHOD'], 'render' => $method];
						
						foreach(explode('/', $url['url']) as $number => $io)
						{
							if($io != '')
							{	
								if(isset($url['param'][$number]))
								{
									$tmp[$url['param'][$number]] = $tmpUrl[$number];
								}
							}
						}
						
						self::$route['route']['param'] = $tmp;
						
						$app = new MRes($url['controller']);
						
						self::$route['route']['classes']['controller'] 	= $url['controller'];
						self::$route['route']['classes']['middleware']	= empty($url['middleware'])? '' : $url['middleware'];
						self::$route['route']['classes']['class'] 		= $app->class;
						self::$route['route']['classes']['method'] 		= $app->method;
						self::$route['route']['classes']['path'] 		= $app->path;
						self::$route['route']['classes']['fullPath']   .= $app->path;
						
						self::$route['route']['classes'] = (object)self::$route['route']['classes'];
						self::$route['route'] 			 = (object)self::$route['route'];
						
						$checking = 1;
						break;
					}
				}
			}
			
			if(isset(self::$route['route']->classes->fullPath))
			{
				$path = Mres::replaceSlash(self::$route['route']->classes->fullPath);
				
				$app = str_replace('/', '\\', $path);
				
				self::$route['route']->classes->fullPath  = $path;
				self::$route['route']->classes->namespace = $app;
			}
		}
		
		return $checking;
	}
	
	public function xdebug()
	{
		print_r(self::$route);
		echo"\n".str_repeat('-',100)."\n";
		print_r(self::$RouteMap);
		echo"\n".str_repeat('-',100)."\n";
	}
}