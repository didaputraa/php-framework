<?php
namespace System\Core\Http;

use System\Route\RouteManager as Route;

class Request
{
	//curl -X PUT -F upload=@D:\a.png  --url http://localhost/fw/front/put
	
	static $request = [
		'data'		=> '', 
		'store' 	=> '', 
		'explode'	=> '', 
		'boundary'	=> '', 
		'type'		=> 'default',
		'type_fetch'=> ''
	];
	
	static $uploadName = '';

	use \System\Core\Protection\ValidateRequest;
	
	
	public function __construct()
	{
		self::$request['data'] = urldecode(file_get_contents('php://input'));
		//echo (file_get_contents('php://input'));
		
	}
	
	public function UrlBeforeRender()
	{
		return substr($_SERVER['PHP_SELF'],0, strpos($_SERVER['PHP_SELF'],'index.php'));
	}
	
	public function UrlAfterRender()
	{
		$res = substr($_SERVER['PHP_SELF'], strpos($_SERVER['PHP_SELF'],'index.php')+9);
		
		return $res == ''? '/' : substr($res,1);
	}
	
	
	public function UrlRender($link = '')
	{
		if(empty($link))
		{
			$url = self::UrlAfterRender();
		}
		else
		{
			$url = $link;
		}
		
		return $url == '/' ? '/' : preg_replace(['/^\//','/\/+$/'],'',$url);
	}
	
	public function input($name = '')
	{
		$data = '';
		$check=0;
		
		/*if(isset($_REQUEST[$name]))
		{
			$data  = $_REQUEST[$name];
			$check = 1;
		}*/

		if($check == 0)
		{
			$data = self::rawInput($name);
		}
		
		return $data;
	}
	
	public function get($name = '')
	{
		if(isset($_GET[$name]))
		{
			return $_GET[$name];
		}
		return;
	}
	
	public function post($name = '')
	{
		if(isset($_POST[$name]))
		{
			return $_POST[$name];
		}
		return;
	}
	
	public function put($name = '')
	{
		return self::rawInput($name);
	}
	
	public function upload($name = '')
	{
		if($name !== '')
		{
			$msg = '';
			
			if(!empty($_POST) && !self::isAjax())
			{
				$file = $_FILES[$name];
				
				if($file['error'] == UPLOAD_ERR_OK)
				{
					if(is_uploaded_file($file['tmp_name']))
					{
						$name= date('dmyHis',strtotime('now')).substr($file['name'],-4);
						$dir = \Config\App::getUploadDistination().$name;
						
						if(move_uploaded_file($file['tmp_name'], $dir))
						{
							return $name;
						}
					}
				}
			}
			else
			{
				$raw = self::rawInput($name, true);
				$ext = '';
				
				if(!empty($raw))
				{
					if($raw->name !== '__undefined__name__')
					{
						$ext = substr($raw->name, -4);
					}
					
					$name= date('dmyHis',strtotime('now')).$ext;
					
					file_put_contents(\Config\App::getUploadDistination().$name, $raw->content);
					
					return $name;
				}
			}
		}
		
		return '';
	}
	
	private function rawInput($name = '', $uploads_ = false)
	{
		if(empty(self::$request['boundary']))
		{
			$file = self::$request['data'];
			
			if(preg_match('/^-+[a-z0-9]+\s/', $file, $res))
			{
				$boundary = $res[0];
				
				self::$request['boundary'] = explode($boundary, $file);
				self::$request['type']	   = 'boundary';
			}
		}
		
		if(self::$request['type'] == 'boundary')
		{
			foreach(self::$request['boundary'] as $record)
			{
				if(preg_match('/Content-Disposition: form-data; name="(.+?)"./', $record, $attribute))
				{
					$str = preg_replace([
						"/^\s{$attribute[0]} filename=.*\s\nContent-Type: .*/",
						'/-{26}[a-zA-Z0-9]+--\s$/',
					], '', $record);
					
					$str = preg_replace([
						'/^\s{3}/',
						'/\s{3}$/'
					], '', $str);
					
					if($name == $attribute[1])
					{
						if($uploads_ == true)
						{
							preg_match('/filename="(.+)"/', $record, $name_file);
							
							$nm = empty($name_file[1])? '' : $name_file[1];
							
							return (object)[
								'name'	 => $nm,
								'content'=> $str
							];
						}
						else
						{
							return $str;
						}
					}
				}
			}
		}
		elseif(self::$request['type'] == 'default')
		{
			$valueArray = [];
			 
			if(empty(self::$request['explode']))
			{
				$__data 	= self::$request['data'];
				$type 		= 'default';
				
				if(substr($__data,0,1) == '{')
				{
					$type	= 'fetch';
				}
				
				if($type == 'fetch')
				{
					$preg = json_decode($__data,true);
					
					self::$request['type_fetch'] = 'fetch';
					self::$request['explode'] 	 = $preg;
				}
				else
				{
					$preg = preg_replace('/[\&](.+?)=/', '{{:---:}}$1=', $__data);
					
					self::$request['explode'] = explode('{{:---:}}', $preg);
				}
			}
			
			if(self::$request['type_fetch'] == 'fetch')
			{
				//echo 'ex1';
				foreach(self::$request['explode'] as $p_field => $p_value)
				{
					
					if($name == $p_field)
					{
						if(preg_match('/\[/', $p_field))
						{
							$valueArray[] = urldecode($p_value);
						}
						else
						{
							return urldecode($p_value);
							break;
						}
					}
					
				}
			}
			else
			{
				//echo 'ex2';
				foreach(self::$request['explode'] as $puts)
				{
					preg_match('/(.+?)=(.*)/',$puts, $put);
					
					if(isset($put[1]))
					{
						if($name == $put[1])
						{
							if(preg_match('/\[/', $put[1]))
							{
								$valueArray[] = urldecode($put[2]);
							}
							else
							{
								if($uploads_ === true)
								{
									$str = preg_replace('/('.$put[1].')=/','',$puts);
									
									return (object)[
										'name' 		=> '__undefined__name__',
										'content'	=>$str
									];
								}
								else
								{
									return urldecode($put[2]);
								}
								
							}
						}
					}
				}
			}
			
			return empty($valueArray)? '' : $valueArray;
		}
		
		return;
	}
	
	public function getHeader()
	{
		return apache_request_headers();
	}
	
	public function isAjax()
	{
		$arh = apache_request_headers();
		
		if(array_key_exists('X-Requested-With', $arh))
		{
			unset($arh);
			
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	public function parameter()
	{
		$rute = Route::getRoutes();
		
		if(preg_match('/_PARAM$/',$rute->method->render))
		{
			return (object)$rute->param;
		}
		
		return;
	}
}