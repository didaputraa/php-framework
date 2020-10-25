<?php
namespace System\Database;

use System\Database\MySQLConnect;


abstract class Model
{
	use Model\ModelBuilder;
	use Model\ModelOperation;
	
	protected static $fillable 		= [];
	protected static $table 		= '';
	
	private static $Active__		= ['method' => '', 'value' => ''];
	
	private $type		= 'create';
	private $columns 	= [];
	
	private $sql = [
		'select'		=> ['*'],
		'select_raw'	=> '',
		'select_add'	=> [],
		'inner_join'	=> [],
		'where'			=> [],
		'where_raw'		=> [],
		'where_in'		=> [],
		'group'			=> [],
		'order'			=> [],
		'limit'			=> 'LIMIT 0,25',
		'relations'		=> []
	];
	
	
	function __construct()
	{
		foreach(static::$fillable as $i)
		{
			$this->columns[$i] = '';
		}
	}
	
	public function __get($name='')
	{
		$instance = self::return();
		
		if($name != '' && isset($instance->columns[$name]))
		{
			return $instance->columns[$name];
		}
	}
	
	public function __set($name = '', $value = '')
	{
		$instance = self::return();
		
		if(isset($name) && !empty($value))
		{
			return $instance->columns[$name] = $value;
		}
	}
	
	final function find($key = '')
	{
		$instance 	= self::return();
		$db  		= new MySQLConnect;
		$id 		= static::$primary;
		$table		= static::$table;
		$where		= $instance->buildWhereAndRaw();
		
		
		if($key != '' && empty($where))
		{
			$where .= " WHERE `{$id}` = \"{$key}\"";
		}
		elseif($key != '' && !empty($where))
		{
			$where .= "&& `{$id}` = \"{$key}\"";
		}
		
		$instance->cacheWhere = $where;
		
		$query	 = "SELECT * FROM {$table}{$where} LIMIT 1";
		$results = $db->fetch($query);
		
		
		if(!empty($results))
		{
			$data = $results[0];
			
			foreach(static::$fillable as $field)
			{
				if(isset($data->{$field}))
				{
					$instance->columns[$field] = $data->{$field};
				}
			}
			
			$instance->type = 'update';
		}
		else
		{
			$instance->type = 'wrong';
		}
		
		return $instance;
	}
	
	final public function relation($foreign = '', $key1 = 'id', $master = '', $key2 = 'id')
	{
		$instance = self::return();
		
		if(empty($master))
		{
			$instance->sql['relations'][] = [
				'master'  => static::$table,
				'foreign' => ($foreign)::getClasses()->table,
				'key1'	  => $key1,
				'key2'	  => $key2
			];
		}
		else
		{
			$instance->sql['relations'][] = [
				'master'  => ($master)::getClasses()->table,
				'foreign' => ($foreign)::getClasses()->table,
				'key1'	  => $key1,
				'key2'	  => $key2
			];
		}
		
		return $instance;
	}
	
	final public function save()
	{
		$instance = self::return();
		
		$db		= new MySQLConnect;
		$table	= static::$table;
		
		$column	= array_keys($instance->columns);
		$value	= array_values($instance->columns);
		
		switch($instance->type)
		{
			case 'create':
				$sql 	= '';
				
				$column = '`'.implode(array_keys($instance->columns), "`,`").'`';
				$value 	= '"'.implode(array_values($instance->columns), '","').'"';
				
				$query  = "INSERT INTO `{$table}` ({$column}) VALUES({$value})";
				
				$db->queryRun($query);
			break;
			
			case 'update':
				$sql	= '';
				$id		= static::$primary;
				$where	= $instance->cacheWhere;
				
				foreach($instance->columns as $key => $val)
				{
					$sql .= "`{$key}` = \"{$val}\",";
				}
				
				$sql 	= substr($sql,0,-1);
				$query  = "UPDATE `{$table}` SET {$sql}{$where}";
				
				$db->queryRun($query);
			break;
			
			case 'wrong':
				# empty/error
			break;
		}
	}
	
	final public function destroy()
	{
		$db 		= new MySQLConnect;
		$key		= '';
		$table		= static::$table;
		$num_arg	= func_num_args();
		$get_args	= func_get_args();
		
		$query	= "DELETE FROM `{$table}`";
		
		if($num_arg > 0)
		{
			if($num_arg == 1 && is_array($get_args[0]))
			{
				$key = '"'.implode($get_args[0], '","').'"';
			}
			else
			{
				$key = '"'.implode($get_args, '","').'"';
			}
			
			$query .= " WHERE `".static::$primary."` IN({$key})";
		}
		
		$db->queryRun($query);
	}
	
	public function debug()
	{
		$id = self::return();
		
		print_r($id->columns);
		print_r($id->columnData);
		
		echo str_repeat('-',80)."\n\n";
	}
}