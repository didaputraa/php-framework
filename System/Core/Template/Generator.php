<?php 
namespace System\Core\Template;

use System\Core\Template\GeneratorRule;


class Generator extends GeneratorRule
{
	public static $content   = ['active' => '', 'content' => [], 'status' => 0];
	private static $path	 = 'Storage/cacheSystem/';
	
	
	private static function numberCache()
	{
		$char = str_shuffle('abcdefghijklmnopqrstuvwxyz');
		
		return substr($char,0,3).date('dmyHis',strtotime('now'));
	}
	
	public static function initCache($fileArg = '', $cache = '', $type = 'php')
	{
		$output = '';
		
		if(file_exists($fileArg))
		{
			$system = self::$path.'template.json';
			
			if(!file_exists($system))
			{
				file_put_contents($system, json_encode([0 => ['name' => 'empty']]));
			}
			
			
			$json   = json_decode(file_get_contents($system));
			$column = array_column($json,'name');
			$no 	= array_search($fileArg, $column);
			$allow  = 0;
			
			
			if($no > 0)
			{
				$output = self::$path.'view/'.$json[$no]->cacheName.'.php';
				$allow  = 1;
				
				if(filemtime($fileArg) != $json[$no]->times)
				{
					if($type == 'theme')
					{
						$cache = self::templateInit($fileArg);
					}
					elseif($type == 'php')
					{
						$cache = file_get_contents($fileArg);
					}
					
					$json[$no]->times = filemtime($fileArg);
					$allow 			  = 0;
				}
			}
			else
			{
				$cacheFile = self::numberCache();
				$output	   = self::$path.'view/'.$cacheFile.'.php';
				$allow 	   = 0;
				
				array_push($json,['name' => $fileArg, 'cacheName' => $cacheFile, 'times' => filemtime($fileArg)]);
			}
			
			if($allow != 1)
			{
				file_put_contents($system, json_encode($json));
				file_put_contents($output, $cache);
			}
		}
		
		return $output;
	}
	
	public static function templateInit($fileArg = '')
	{
		$cache = '';
		
		if(file_exists($fileArg))
		{
			$str = file_get_contents($fileArg);
			
			$cache = preg_replace(parent::$pattern, parent::$replacement, $str);
		}
		
		return $cache;
	}
	
	public static function phpSectionExtends($name = '')
	{
		self::$content['extends'] 	= $name;
		self::$content['status'] 	= 1;
	}
	
	public static function phpImportTemplate($name = '')
	{
		$file = "App/View/{$name}.theme.php";
		
		if($name != '' && file_exists($file))
		{
			$content = self::initCache($file, self::templateInit($file), 'theme');
			
			if(file_exists($content))
			{
				include $content;
			}
		}
	}
	
	public static function phpSection_show($name = '')
	{
		if(isset(self::$content['content'][$name]))
		{
			echo htmlspecialchars_decode(self::$content['content'][$name],ENT_QUOTES);
		}
	}
	
	public static function phpSectionStart($name = '',$value = '')
	{
		if(empty($value))
		{
			self::$content['active'] = $name;
			ob_start();
		}
		else
		{
			self::$content['content'][$name] = $value;
		}
	}
	
	public static function phpSectionEnd()
	{
		if(isset(self::$content['active']))
		{
			self::$content['content'][self::$content['active']] = htmlspecialchars(ob_get_contents(),ENT_QUOTES);
			self::$content['active'] = '';
			
			ob_end_clean();
		}
	}
	
	public static function phpSectionExtends_show()
	{
		$buffer = '';
		
		if(self::$content['status'] == 1)
		{
			$file = self::$content['extends'].'.theme.php';
			
			$init = 'App/View/'.$file;
			
			if(file_exists($init))
			{
				$extends = self::initCache($init, self::templateInit($init), 'theme');
				
				if($extends != '')
				{
					require_once $extends;
				}
			}
			else
			{
				echo $init;
			}
		}
		else
		{
			return htmlspecialchars_decode($buffer ,ENT_QUOTES);
		}
	}

	public static function phpErrorValidate($name = '')
	{
		$target = 'err__'.$name.'__enderr';

		if($name != '' && isset($_SESSION[$target]))
		{
			return [
				'val' => array_values($_SESSION[$target])[0],
				'key' => array_keys($_SESSION[$target])[0]
			];
		}
		
		return ['val' => '', 'key' => ''];
	}

	public static function endphpErrorValidation($name)
	{
		unset($_SESSION['err__'.$name.'__enderr']);
	}
}