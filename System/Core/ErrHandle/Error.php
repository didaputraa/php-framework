<?php
namespace System\Core\ErrHandle;

use \Config\ErrorRule;


final class Error
{
	private static $path = '';
	
	
	final public static function analize($location = '')
	{
		self::$path = $location;
		
		register_shutdown_function(function(){
		
			self::shutdown();
		});
	}

	final private static function response_errJson($data)
	{
		header('Content-Type: application/json');

		echo json_encode($data);
	}

	final private static function isProduction()
	{
		if(\Config\App::getProduction() && !\Config\App::getMaintenance())
		{
			if(self::isCommand())
			{
				self::response_errJson([
					'message' => ErrorRule::getMessage('production')
				]);
			}
			else
			{
				$message = ErrorRule::getMessage('production');

				require_once 'prod.php';
			}

			exit();
		}
	}

	final public static function isMaintenance()
	{
		if(\Config\App::getMaintenance())
		{
			if(self::isCommand())
			{
				self::response_errJson([
					'message' => ErrorRule::getMessage('maintenance')
				]);
			}
			else
			{
				$message = ErrorRule::getMessage('maintenance');

				require 'maintenance.php';
			}
			exit();
		}
	}

	private static function isCommand()
	{
		self::loadClass_external();
		
		$ajax = new \System\Core\Http\Request;
		$curl = '';

		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$curl = preg_match('/curl/', $_SERVER['HTTP_USER_AGENT']);
		}

		if($ajax->isAjax() || !empty($curl))
		{
			return 1;
		}

		return 0;
	}
	
	public static function shutdown()
	{
		$err = error_get_last();

		if(!empty($err))
		{
			if(!class_exists(\Config\ErrorRule::class))
			{
				require self::$path.'Config/ErrorRule.php';
			}

			return self::errorCode($err['type'], $err['message'], $err['file'], $err['line']);
		}
	}
	
	public static function errorCode($type, $msg, $file, $line)
	{
		if($type == E_DEPRECATED)
		{
			restore_error_handler();
		}
		else
		{
			ob_end_clean();
			http_response_code(500);
			
			if(self::isCommand())
			{
				self::isProduction();

				self::response_errJson([
					'status'	=> 500,
					"message" 	=> $msg,
					'filename'	=> $file,
					'line'		=> $line
				]);
			}
			else
			{
				self::isProduction();

				require 'ErrorResponse.php';

				$err = new \System\Core\ErrHandle\ErrorResponse(self::$path, $type, $msg, $file, $line);
				
				$err->response();
			}
			
			$handle	= fopen(self::$path.'Storage/logs/error_'.date('dmy', strtotime('now')), 'a');

			fwrite($handle, date('H:i:s',strtotime('now'))." {$file}:{$line}\n");
			fclose($handle);

			exit();
		}
	}
	
	final private static function loadClass_external()
	{
		if(!class_exists(\System\Core\Http\Request::class))
		{
			require self::$path.'System/Core/Http/Request.php';
		}
	}

	final public static function error404()
	{
		http_response_code(404);

		if(self::isCommand())
		{
			self::isProduction();

			self::response_errJson([
				'status'	=> 404,
				"message" 	=> 'Halaman tidak ditemukan'
			]);
		}
		else
		{
			self::isProduction();

			require '404.php';
		}

		exit();
	}

	final public static function errorExpired()
	{
		header('HTTP/1.1 419 Unknown Status');

		if(self::isCommand())
		{
			self::isProduction();

			self::response_errJson([
				'status'	=> 419,
				"message" 	=> 'Halaman kadaluarsa'
			]);
		}
		else
		{
			self::isProduction();

			require '419.php';
		}

		exit();
	}
}