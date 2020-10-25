<?php
namespace System\Core\Session;

class SessionManual
{
	protected static $char = 'abcdefghijklmnopqrstuvwxyz';
	
	
	function dir_id()
	{
		$s	= '';
		$a1 = 'axdriputyseonc';
		
		for($i=0; $i<3; $i++)
		{
			$s .= substr(str_shuffle($a1),2,1).substr(str_shuffle(self::$char),5,1);
		}
		
		return $s;
	}
	
	function extractDir($id, $path)
	{
		$tmp = '';
		
		for($i=0; $i<3; $i++)
		{
			$tmp .= '/'.substr($path, $i+$i,2);
		}
		
		return $tmp;
	}
	
	function createDir($path, $id)
	{
		$dir = $path;
		
		for($i=0; $i<3; $i++)
		{
			$key = substr($id, $i+$i, 2);
			
			if(in_array($key, scandir($dir)))
			{
				$dir .= '/'.$key;
			}
			else
			{
				$dir .= '/'.$key;
				
				mkdir($dir);
			}
		}
		
		return $dir;
	}
	
	function charRand($int = 3)
	{
		$str 	= '';
		
		switch(rand(0,1))
		{
			case 0:
				for($i=0; $i<$int; $i++)
				{
					$str .= rand(0,9);
				}
			break;
			case 1:
				$str .= substr(str_shuffle(self::$char),0,$int);
			break;
		}
		
		return $str;
	}
	
	function getDate_($a = '')
	{
		return date($a, strtotime('now'));
	}
	
	function getDirID($id)
	{
		return substr($id, 0, 6);
	}
	
	function getFileID($id)
	{
		$file = '';
		
		for($i=0; $i<6; $i++)
		{
			$file .= substr($id,11+(5*$i),2);
		}
		
		return $file.substr($id,-3);
	}
}