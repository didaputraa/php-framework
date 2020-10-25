<?php
namespace System\Core\ErrHandle;

class ErrorResponse
{
	private $errors 	= [];
	private $fakePath	= '';
	
	public function __construct($path, $type, $msg, $file, $line)
	{
		$this->errors = (object)[
			'type'	=> $type,
			'msg'	=> $msg,
			'file'	=> $file,
			'line'	=> $line
		];
		
		$this->fakePath = $path;
	}
	
	public function response()
	{
		include 'panel.php';
	}
}