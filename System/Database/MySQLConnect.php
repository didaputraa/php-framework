<?php
namespace System\Database;

use System\Core\ErrHandle\Error;
use Config\App;
use Exception;
use mysqli;


class MySQLConnect
{
	private $dsn;
	
	public function __construct()
	{
		$app = App::getDB();
		
		$this->dsn = new mysqli($app->host, $app->username, $app->password, $app->dbname, $app->port);
		
		if($this->dsn->connect_error)
		{
			$err = debug_backtrace();
			
			Error::errorCode(0, $err[0]['object']->dsn->connect_error, $err[1]['file'], $err[1]['line']);
		}
	}
	
	final public function queryRun($sql)
	{
		try
		{
			$this->dsn->query($sql);
			
			if($this->dsn->error)
			{
				throw new Exception($this->dsn->error);
			}
		}
		catch(Exception $err)
		{
			$trace = $err->getTrace();
			
			Error::errorCode($err->getCode(), $err->getMessage(), $trace[1]['file'], $trace[1]['line']);
		}
	}
	
	final public function fetch($query = '')
	{
		try
		{
			$sql = $this->dsn->query($query);
			$tmp = [];
			
			if($this->dsn->error)
			{
				throw new Exception($this->dsn->error);
			}
			else
			{
				while($r = $sql->fetch_object())
				{
					$tmp[] = $r;
				}
				
				return $tmp;
			}
		}
		catch(Exception $err)
		{
			$trace = $err->getTrace();
			
			Error::errorCode($err->getCode(), $err->getMessage(), $trace[1]['file'], $trace[1]['line']);
		}
	}
}