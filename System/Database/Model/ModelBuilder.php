<?php
namespace System\Database\Model;

trait ModelBuilder
{
	public function getClasses()
	{
		return (object)[
			'table' 	=> static::$table,
			'primary'	=> static::$primary
		];
	}
	
	public function renderForQuery()
	{
		$instance = self::return();
		
		$sql 	  	= $instance->sql;
		$select	  	= $instance->buildSelects($instance);
		$table	  	= $instance->getStaticTable();
		
		$inner_join = $instance->buildJoin($instance);
		
		$order		= $instance->buildOrders($sql);
		$group		= $instance->buildGroups($sql['group']);
		
		$from		= [];
		$fromWhere  = '';
		
		$whereBuild = $instance->buildWhereAndRaw();
		
		$ssql 		= $instance->buildRelations($sql);
		
		$from 		= [$table, ...$ssql['from']];
		$from 		= implode(array_unique($from), ',');
		
		if(!empty($ssql['fromWhere']) && $whereBuild != '')
		{
			$whereBuild .= ' && '.$ssql['fromWhere'];
		}
		elseif(!empty($ssql['fromWhere']) && $whereBuild == '')
		{
			$whereBuild .= ' WHERE '.$ssql['fromWhere'];
		}
		
		return "SELECT {$select} FROM {$from}{$inner_join}{$whereBuild}{$group}{$order} ";
	}
	
	private function buildRelations($sql)
	{
		$from	= [];
		$tmp	= '';
		
		if(!empty($sql['relations']))
		{
			foreach($sql['relations'] as $opt)
			{
				$from[] = $opt['foreign'];
				$from[] = $opt['master'];

				$tmp .= "`{$opt['foreign']}`.`{$opt['key1']}` = `{$opt['master']}`.`{$opt['key2']}` && ";
			}
			
			$tmp = substr($tmp,0,-4);
		}
		
		return ['from' => $from, 'fromWhere' => $tmp];
	}
	
	private function buildSelects($instance)
	{
		$table 	= $instance->getStaticTable();
		$select = '';
		$sql 	= $instance->sql;
		
		if((count($sql['select']) == 1 && $sql['select'][0] == '*') || empty($sql['select']))
		{
			$select .= "`{$table}`.*";
		}
		else
		{
			if(!empty($sql['select']))
			{
				$select = '`'.implode($sql['select'], '`,`').'`';
				$select = str_replace([
					'.',
					'`*`'
				],
				[
					'`.`',
					'*'
				], $select);
			}
		}
		
		if($sql['select_raw'] != '')
		{
			if(empty($select))
			{
				$select .= $sql['select_raw'];
			}
			else
			{
				$select .= ','.$sql['select_raw'];
			}
		}
		
		if(!empty($sql['select_add']))
		{
			$adds = '';
			
			foreach($sql['select_add'] as $add)
			{
				$adds .= $add.',';
			}
			
			$adds = substr($adds, 0, -1);
			
			$select .= empty($select)? $adds : ','.$adds;
		}
		
		return $select;
	}
	
	private function buildGroups($sql)
	{
		return empty($sql)? '' : $sql;
	}
	
	private function buildOrders($sql)
	{
		$tmp = '';
		
		if(!empty($sql['order']))
		{
			$tmp = $sql['order'];
		}
		
		return $tmp;
	}
	
	final private function buildWhere($sql)
	{
		$tmp 	= '';
		$where	= '';
		$table	= self::return()->getStaticTable();
		
		foreach($sql['where'] as $row)
		{
			$tmp .= "`{$table}`.`{$row['column']}` {$row['opt']} \"{$row['sql']}\" && ";
		}
		
		if(!empty($tmp))
		{
			$where = substr($tmp, 0, -4);
		}
		
		return $where;
	}
	
	final private function buildWhereRaw($sql)
	{
		$tmp = '';
		
		if(!empty($sql['where_raw']))
		{
			$tmp = implode($sql['where_raw']," && ");
		}
		
		return $tmp;
	}
	
	private function buildWhereIn()
	{
		
	}
	
	private function buildJoin($instance)
	{
		$tmp = '';
		
		if(!empty($instance->sql['inner_join']))
		{
			$tmp .= ' ';

			foreach($instance->sql['inner_join'] as $join)
			{
				$tmp .= "{$join} ";
			}
		}
		

		return $tmp != ''? substr($tmp,0,-1) : '';
	}
	
	private function buildWhereAndRaw()
	{
		$instance 	= self::return();

		$sql 		= $instance->sql;
		$dev 		= $instance->buildWhere($sql);
		$raw 		= $instance->buildWhereRaw($sql);
		$where		= '';
		
		if(empty($dev) && !empty($raw))
		{
			$where .= " WHERE {$raw}";
		}
		elseif(!empty($dev) && empty($raw))
		{
			$where .= " WHERE {$dev}";
		}
		elseif(!empty($dev) && !empty($raw))
		{
			$where .= " WHERE {$dev} && {$raw}";
		}
		
		return $where;
	}
}