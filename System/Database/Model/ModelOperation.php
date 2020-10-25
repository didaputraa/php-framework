<?php
namespace System\Database\Model;

use System\Database\MySQLConnect;

trait ModelOperation
{
	private $cacheWhere	= '';
	

	final private function return()
	{
		$class = static::class;
		
		if(self::$Active__['method'] == '')
		{
			if(isset($this))
			{
				return $this;
			}
			
			return new static;
		}
		else
		{
			$return = self::$Active__['value'];
			
			self::$Active__['method'] = '';
			self::$Active__['value']  = '';
			
			return $return;
		}
	}
	
	final private function getStaticTable()
	{
		$instance = self::return();
		
		if(isset(static::$table))
		{
			return static::$table;
		}
		
		return $instance->sql['table'];
	}
	
	private function ActiveObjects__($method, $value)
	{
		$instance = self::return();
		
		$instance::$Active__['method'] 	= $method;
		$instance::$Active__['value'] 	= $value;
	}
	
	final function select($sql = null)
	{
		$instance = self::return();
		
		if(is_null($sql))
		{
			$instance->sql['select'] = [];
		}
		else
		{
			$instance->sql['select'] = $sql;
		}
		return $instance;
	}

	final function selectRaw($sql = '')
	{
		$instance = self::return();
		
		if(!empty($sql))
		{
			$instance->sql['select_raw'] = $sql;
		}
		
		return $instance;
	}

	final function addSelect($sql = '')
	{
		$instance = self::return();
		
		if(!empty($sql))
		{
			$instance->sql['select_add'][] = $sql;
		}
		
		return $instance;
	}
	
	final function count()
	{
		$instance   = self::return();
		
		$db 		= new MySQLConnect;
		$sql 		= $instance->renderForQuery();
		$count		= count($db->fetch($sql));
		
		$instance->ActiveObjects__('count', $count);
		
		return self::return();
	}
	
	final function where($column = '', $opt = '', $argument = null)
	{
		$instance = self::return();
		
		if(!empty($column))
		{
			if(empty($argument))
			{
				if(is_numeric($argument))
				{
					$instance->sql['where'][] = [
						'column' => $column,
						'opt'	 =>	$opt,
						'sql'	 => $argument
					];
				}
				else
				{
					$instance->sql['where'][] = [
						'column' => $column,
						'opt'	 =>	'=',
						'sql'	 => $opt
					];
				}
			}
			else
			{
				if(!empty($argument))
				{
					$value = (int)$argument;
				}
				else
				{
					$value = '"'.($argument).'"';
				}
				
				$instance->sql['where'][] = [
					'column' => $column,
					'opt'	 =>	$opt,
					'sql'	 => $value
				];
			}
		}
		
		return $instance;
	}

	final function whereIn($column, $data = []) // ------------------ maintenance
	{
		$instance	= self::return();
		$field		= '';
		$data		= implode($data, '","');
		
		if(preg_match('/\./',$column))
		{
			$col	= explode('.',$column);
			$field	= "`{$col[0]}`.`{$col[1]}`";
			
			$instance->sql['where_in'] = "{$field} IN (\"{$data}\")";
		}
		else
		{
			$field = '`'.static::$table."`.`{$column}`";
			
			$instance->sql['where_in'] = "{$field} IN (\"{$data}\")";
		}
		
		return $instance;
	}

	final function orWhere() // ------------------ maintenance
	{

	}

	final function whereDate() // ------------------ maintenance
	{

	}
	
	final function whereRaw($raw = '')
	{
		$instance = self::return();
		
		if(!empty($raw))
		{
			$instance->sql['where_raw'][] = $raw;
		}
		
		return $instance;
	}
	
	final function groupBy($column = '')
	{
		$instance = self::return();
		
		if(!empty($column))
		{
			if(preg_match('/\./', $column))
			{
				$ex = explode('.', $column);
				
				$tmp = "`{$ex[0]}`.`{$ex[1]}`"; 
			}
			else
			{
				$tmp = '`'.static::$table."`.`{$column}`";
			}
			
			$instance->sql['group'] = " GROUP BY {$tmp}";
		}
		
		return $instance;
	}
	
	final function orderBy($column = '', $sort = 'ASC')
	{
		$instance = self::return();
		$tmp	  = '';
		$sort	  = strtoupper($sort);
		
		if(!empty($column))
		{
			if(preg_match('/\./', $column))
			{
				$ex = explode('.', $column);
				
				$tmp = "`{$ex[0]}`.`{$ex[1]}`"; 
			}
			else
			{
				$tmp = '`'.static::$table."`.`{$column}`";
			}
			
			$instance->sql['order'] = " ORDER BY {$tmp} {$sort}";
		}
		
		return $instance;
	}
	
	final function limit($from = 0, $to = 25)
	{
		$instance = self::return();
		
		$instance->sql['limit'] = "LIMIT {$from},{$to}";
		
		return $instance;
	}
	
	final public function all()
	{
		$instance	= self::return();
		
		$db 		= new MySQLConnect;
		$query  	= $instance->renderForQuery();
		
		return $db->fetch($query);
	}
	
	final function get()
	{
		$instance = self::return();
		
		$db 	= new MySQLConnect;
		$query  = $instance->renderForQuery().$instance->sql['limit'];
		
		return $db->fetch($query);
	}
	
	final function first()
	{
		$instance	= self::return();
		$db 		= new MySQLConnect;
		$query		= $instance->renderForQuery().'LIMIT 0,1';
		$result 	= $db->fetch($query);
		
		if(empty($result))
		{
			$result = [];
		}
		else
		{
			$result = $result[0];
		}
		
		return $result;
	}
	
	final function join($reference, $opt, $master)
	{
		$instance = self::return();
		
		if(!empty($reference))
		{
			$ex  = explode('.',$reference);
			$ex2 = explode('.',$master);

			$instance->sql['inner_join'][] = "INNER JOIN `{$ex[0]}` ON `{$ex[0]}`.`{$ex[1]}` {$opt} `{$ex2[0]}`.`{$ex2[1]}`";
		}
		
		return $instance;
	}
	
	final function delete()
	{
		$instance = self::return();
		
		$db		  = new MySQLConnect;
		
		$table	  = $instance->getStaticTable();
		$where	  = $instance->buildWhereAndRaw();
		
		$query 	  = "DELETE FROM {$table}{$where}";
		
		$db->queryRun($query);
	}
	
	final function create($opt = [])
	{
		$instance = self::return();
		
		$db		= new MySQLConnect;
		
		$table	= $instance->getStaticTable();
		
		if(!empty($opt))
		{
			$key 	= '(`'.implode(array_keys($opt), '`,`').'`)';
			$val 	= '("'.implode(array_values($opt), '","').'")';
			
			$query	= "INSERT INTO {$table} {$key} VALUES {$val}";
			
			$db->queryRun($query);
		}
	}
	
	final function update($opt = [])
	{
		$instance = self::return();
		
		$db		= new MySQLConnect;
		$sql 	= '';
		
		$table	= $instance->getStaticTable();
		$where  = $instance->buildWhereAndRaw();
		
		if(!empty($opt))
		{
			foreach($opt as $k => $v)
			{
				$sql .= '`'.$k.'` = "'.$v.'",';
			}
			
			$sql = substr($sql,0,-1);
			
			$query = "UPDATE {$table} SET {$sql}{$where}";
		
			$db->queryRun($query);
		}
	}
}