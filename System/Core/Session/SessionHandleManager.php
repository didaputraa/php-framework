<?php
namespace System\Core\Session;

class SessionHandleManager extends SessionManual implements \SessionHandlerInterface, \SessionIdInterface
{
	private $savePath = '';
	
	
	function create_sid()
	{
		$str = parent::dir_id().parent::charRand(5);
		
		foreach(['H','d','s','y','i','m'] as $i)
		{
			$str .= parent::getDate_($i).parent::charRand();
		}
		
		return $str;
	}
	
	function open($save, $nm)
	{
		$this->savePath = $save;
		
		return true;
	}
	
	function close()
	{
		return true;
	}
	
	function read($id)
	{
		if(strlen($id) == 41)
		{
			$file = parent::getFileID($id);
			$dirs = parent::getDirID($id);
			
			if(preg_match('/^([a-z]+)$/', $dirs) && preg_match('/^([0-9]+)$/',substr($file,0,-3)))
			{
				$filename = $this->savePath.self::extractDir($id, $dirs).'/'.$file;
				
				if(file_exists($filename))
				{
					return (string)@file_get_contents($filename);
				}
			}
		}
		
		return '';
	}
	
	function write($id,$data)
	{
		if(strlen($id) == 41)
		{
			$file = parent::getFileID($id);
			$dirs = parent::getDirID($id);
			
			if(preg_match('/^([a-z]+)$/', $dirs) && preg_match('/^([0-9]+)$/', substr($file,0,-3)))
			{
				$dir  = parent::createDir($this->savePath, $dirs);
				
				return file_put_contents($dir.'/'.$file, $data) === false? false : true;
			}
		}
		
		return true;
	}
	
	function gc($maxlifetime)
	{
		return true;
	}
	
	function destroy($id)
	{
		return true;
	}
}