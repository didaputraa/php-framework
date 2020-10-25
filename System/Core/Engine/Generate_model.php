<?php
use \Config\App;
use \System\Database\MySQLConnect as DB;
use \System\Core\Engine\Console;

$db = new DB;
$i  = 1;


foreach($db->fetch("show tables") as $row)
{
	$table_name = $row->{'Tables_in_'. App::getDB()->dbname};
	$fields 	= [];
	$primary 	= '';
	
	foreach($db->fetch('desc '.$table_name) as $desc)
	{
		$fields[] = $desc->Field;
		
		if($desc->Key == 'PRI')
		{
			$primary = $desc->Field;
		}
	}
	
	$field = '"'.implode($fields, '", "').'"';
	
	$open = fopen($path.'App/'.ucfirst($table_name).'.php', 'w');
	
	$txt  = '<?php';
	$txt .= "\nnamespace App;\n\n";
	$txt .= "use System\\Database\\Model;\n\n";
	$txt .= 'class '.ucfirst($table_name)." extends Model\n{\n";
	
	$txt .= "\tprotected static \$table		= '{$table_name}';\n";
	$txt .= "\tprotected static \$primary	= '{$primary}';\n";
	$txt .= "\tprotected static \$fillable	= [{$field}];\n";
	
	$txt .= "}\n";
	
	fwrite($open, $txt);
	fclose($open);
	
	echo Console::log("{$i}.", brown);
	echo Console::log("Model", light_blue).
		 Console::log("{$table_name}\t\t", light_green).
		 Console::log("SUCCESS\n", green);
	
	$i++;
}