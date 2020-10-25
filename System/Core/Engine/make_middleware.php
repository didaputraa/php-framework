<?php
use \System\Core\Engine\Console;

$str  = '<?php'.PHP_EOL;
$str .= 'namespace App\Middleware;'.PHP_EOL .PHP_EOL;
$str .= 'use System\Core\Http\Request;'.PHP_EOL;
$str .= 'use System\Core\Auth as Authentication;'.PHP_EOL . PHP_EOL;
$str .= 'class '.$class_name.PHP_EOL .'{';
$str .= "\n\tpublic function handle(Request \$request)\n\t{\n\t\t//tulis di sini\n\t}\n}";

$open = fopen('App/Middleware/'.$class_name.'.php', 'w');
fwrite($open,$str);
fclose($open);

echo 
Console::log('Middleware ', light_blue).
Console::log($class_name, light_green).
Console::log(' SUCCESS'.PHP_EOL, green).
Console::log('location: ', brown);

echo Console::color('App/Middleware/'.$class_name.'.php'.PHP_EOL, white, bg_blue);