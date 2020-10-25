<?php
namespace Config;

use System\Core\Config;

class App extends Config
{
	protected static $app		= 'Name';
	
	protected static $production = false;
	protected static $maintenance= false;
	
	protected static $site		= [
		'domain'	=> 'localhost',
		'protocol'	=> 'http',
		'port'		=> 80
	];
	
	
	protected static $mysql		= [
		'host'		=> 'localhost',
		'username'	=> 'root',
		'password'	=> '',
		'dbname'	=> 'testing',
		'port'		=> 3306,
	];
	
	
	protected static $session 	= [
		'name'		=> 'Native-Nusa',
		'lifetime'	=> 3600,
		'httpOnly'	=> true,
		'secure'	=> false,
		'domain'	=> 'localhost',
	];
	
	
	
	protected static $uploadDist = 'Asset/';
	
	
	
	protected static $timezone	= 'Asia/jakarta';
	
	
	protected static $protection= [
		'csrf' => 300
	];
	
	
	protected static $guards 	= [
		'model' 	=> \App\User::class,
	];
}