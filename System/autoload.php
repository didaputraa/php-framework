<?php
namespace System;

class autoload
{
	public static function loader()
	{
		spl_autoload_register(function($class_require){

			if(isset($class_require))
			{
				$file = str_replace("\\","/", $class_require);
				
				if(file_exists($file.'.php'))
				{
					require_once $file.'.php';
				}
			}
		});
	}
}