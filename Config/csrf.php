<?php
namespace Config;

class csrf
{
	protected $method = ['POST','PUT','DELETE'];
	
	protected $except = [
		'POST' 	 => [],
		'PUT' 	 => [],
		'DELETE' => []
	];
}