<?php
use \Config\App;
use \System\Database\MySQLConnect as DB;
use \System\Core\Engine\Console;

$fields 	= [];
$primary 	= '';
$db 		= new DB;
$result 	= $db->fetch('desc '.$table);

if(count($result) > 0)
{
	foreach($result as $desc)
	{
		$fields[] = $desc->Field;
		
		if($desc->Key == 'PRI')
		{
			$primary = $desc->Field;
		}
	}

	$field = '"'.implode($fields, '", "').'"';

	$open = fopen($path.'App/'.ucfirst($table).'.php', 'w');

	$txt  = '<?php';
	$txt .= "\nnamespace App;\n\n";
	$txt .= "use System\\Database\\Model;\n\n";
	$txt .= 'class '.ucfirst($table)." extends Model\n{\n";
	$txt .= "\tprotected static \$table		= '{$table}';\n";
	$txt .= "\tprotected static \$primary	= '{$primary}';\n";
	$txt .= "\tprotected static \$fillable	= [{$field}];\n";
	$txt .= "}\n";

	fwrite($open, $txt);
	fclose($open);

	echo Console::log("Model ", light_blue).
		 Console::log($table, light_green).
		 Console::log(" SUCCESS\n", green);
}
else
{
	echo "Model {$table}\tFAILED\n";
}