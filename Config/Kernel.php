<?php
namespace Config;

class Kernel
{
	public $middlewareRoute = [
		'Auth' =>  \App\Middleware\Auth::class
	];
	
	
	public $serviceRoute	= [
		'csrf' => \App\Service\CSRF_Provider::class
	];
}