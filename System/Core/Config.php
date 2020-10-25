<?php
namespace System\Core;

abstract class Config
{
	public static function getApp(){
		return static::$app;
	}
	
	public static function getDB()
	{
		return (object)static::$mysql;
	}
	
	public static function getUploadDistination()
	{
		return static::$uploadDist;
	}
	
	public static function getSession()
	{
		return (object)static::$session;
	}
	
	public static function getSite()
	{
		return (object)static::$site;
	}
	
	public static function getTimezone()
	{
		return static::$timezone;
	}
	
	public static function getProtection()
	{
		return (object)static::$protection;
	}
	
	public static function getGuard()
	{
		return (object)static::$guards;
	}

	public static function getProduction()
	{
		return static::$production;
	}
	public static function getMaintenance()
	{
		return static::$maintenance;
	}
}