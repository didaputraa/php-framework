<?php
namespace System\Core\Session;

use Config\App;

class SessionDestroy extends SessionManual
{
	static function execute($path)
	{
		$id = session_id();
		
		if(strlen($id) == 41)
		{
			$file = parent::getFileID($id);
			$dirs = parent::getDirID($id);
			
			if(preg_match('/^([a-z]+)$/',$dirs) && preg_match('/^([0-9]+)$/', substr($file,0,-3)))
			{
				$dir_name = $path.self::extractDir($id, $dirs);
				
				if(is_dir($dir_name))
				{
					foreach(scandir($dir_name) as $r)
					{
						$f = $dir_name.'/'.$r;
						
						if($r != '.' && $r !=  '..' && (filemtime($f) + App::getSession()->lifetime < time()) && file_exists($f))
						{
							@unlink($f);
						}
					}
				}
			}
		}
	}
}