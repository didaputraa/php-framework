<?php
use \System\Core\Engine\Console;


$str  = '<?php'.PHP_EOL .'namespace App\Service'.PHP_EOL .PHP_EOL;
$str .= 'use System\Core\Http\Request;'.PHP_EOL .PHP_EOL;
$str .= "class {$class_name}\n{\n";
$str .= "\tpublic function register()\n\t{\n";
$str .= "\t\t/* taruh di sini */\n";
$str .= "\t}\n}";

$open = fopen('App/Service/'.$class_name.'.php', 'w');

fwrite($open, $str);
fclose($open);

echo 
Console::log('Service ', light_blue).
Console::log($class_name, light_green).
Console::log(' SUCCESS'.PHP_EOL, green).
Console::log('location: ', brown);

echo Console::color('App/Service/'.$class_name.'.php'.PHP_EOL, white, bg_blue);