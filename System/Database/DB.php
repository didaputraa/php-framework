<?php
namespace System\Database;

use System\Database\MySQLConnect;

class DB
{
	use Model\ModelBuilder;
	use Model\ModelOperation;
	
	
	private static $Active__ = ['method' => '', 'value' => ''];
	
	private $sql = [
		'table'			=> '',
		'select'		=> ['*'],
		'select_raw'	=> '',
		'select_add'	=> [],
		'inner_join'	=> [],
		'where'			=> [],
		'where_raw'		=> [],
		'group'			=> [],
		'order'			=> [],
		'limit'			=> 'LIMIT 0,25',
	];
	
	
	public function table($name = '')
	{
		$id = self::return();
		
		if($name != '')
		{
			$id->sql['table'] = $name;
		}
		
		return $id;
	}
}