<?php
namespace System\Core\Cache;


class CacheManager
{
	private $path 			= 'Storage/cache/';
	private $template		= '';
	
	private $char			= 'fghijklmn';
	private $dirCount		= 3;
	private $tmp			= [];
	
	
	public function open($filename = '')
	{
		if($filename != '' && file_exists($this->path.$filename.'.json'))
		{
			$this->template = $filename;
			
			return true;
		}
		
		return false;
	}
	
	public function create($filename = '')
	{
		file_put_contents($this->path."{$filename}.json",'[]');
		
		$this->template = $filename;
	}
	
	public function put($name, $data)
	{
		$extract = $this->extractData();
		
		if(!isset($extract[$name]['path']))
		{
			$file = $this->getFilename($this->createDir());
			
			$extract[$name]['path'] = $file.'.json';
		}
		
		$this->save($extract);
		$this->reinitDir($extract[$name]['path']);
		
		file_put_contents($this->path.'data/'.$extract[$name]['path'], json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE));
	}
	
	public function destroy()
	{
		$cache = $this->extractData();
		$path  = $this->path.'data/';
		
		foreach($cache as $key => $val)
		{
			unlink($path.$val['path']);
		}
		
		unlink($this->path.$this->template.'.json');
	}
	
	public function remove($name = '')
	{
		$cache = $this->extractData();
		
		if(isset($cache[$name]))
		{
			$file   = $this->path.'data/'.$cache[$name]['path'];
			
			$filter = array_filter($cache, function($v) use($name){
					
				return $v !== $name;
				
			},ARRAY_FILTER_USE_KEY);
			
			$this->save($filter);
				
			if(file_exists($file))
			{
				unlink($file);
			}
		}
	}
	
	public function has($name = '')
	{
		$cache = $this->extractData();
		
		if(isset($cache[$name]))
		{
			unset($cache);
			
			return true;
		}
		
		unset($cache);
		
		return false;
	}
	
	public function get($name = '', $type = '')
	{
		$cache = $this->extractData();
		
		if(isset($cache[$name]))
		{
			$file  = $this->path.'data/'.$cache[$name]['path'];
			
			if(file_exists($file))
			{
				$data = file_get_contents($file);
				
				unset($cache);
				
				if($type === 'php')
				{
					return json_decode($data);
				}
				
				return $data;
			}
		}
		
		return [];
	}
	
	
	
	function save($data)
	{
		$data = json_encode($data,JSON_INVALID_UTF8_SUBSTITUTE);
		
		file_put_contents($this->path.$this->template.'.json', $data);
	}
	
	function getFilename($path = '')
	{
		$unique	= scandir("{$this->path}data/{$path}");
		$chr  	= 'abcdefghijklmnopqrstuvwxyz';
		
		while(true)
		{
			$time = date('ymdHis',strtotime('now'));
			$name = str_shuffle(substr(str_shuffle($chr),3,5).$time);
			
			if(!in_array($name, $unique))
			{
				return $path.$name;
			}
		}
		
	}
	
	function reinitDir($path)
	{
		$real_path  = $this->path.'/data/';
		$tmp 		= '';
		
		foreach(array_slice(explode('/', $path), 0, -1) as $dir)
		{
			$tmp .= $dir.'/';
			
			if(!is_dir($real_path.$tmp))
			{
				mkdir($real_path.$tmp);
			}
		}
	}
	
	function createDir()
	{
		$tmp = '';
		$path= $this->path.'data/';
		
		for($i=1; $i <= $this->dirCount; $i++)
		{
			while(true)
			{
				$str 	= str_shuffle($this->char);
				
				$tmp   .= substr($str, 0,2).'/';
				
				if(is_dir($path.$tmp))
				{
					break;
				}
				else
				{
					mkdir($path.$tmp);	
					break;
				}
			}
		}
		
		return $tmp;
	}
	
	
	function extractData()
	{
		return json_decode(file_get_contents($this->path.$this->template.'.json'), true);
	}
}