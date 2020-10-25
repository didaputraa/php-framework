<?php
use \System\Core\Engine\Console;

$txt  = '<?php'.PHP_EOL;
$txt .= '/*'.PHP_EOL .' *Created: '.date('d-m-Y H:i:s').PHP_EOL;
$txt .= 'namespace App\Controller;'.PHP_EOL .PHP_EOL;
$txt .= 'use System\Controllers\Controller;'.PHP_EOL .'use System\Core\Http\Request;'.PHP_EOL .PHP_EOL;

$txt .= 'class '.$class_name.' extends Controller'.PHP_EOL .'{'.PHP_EOL;

$txt .= "\tpublic function index()\n\t{\n\t\t//index method\n\t\t//return view('home');\n\t}";
$txt .= "\n\n\tpublic function create(Request \$request)\n\t{\n\t\t//create method\n\t}";
$txt .= "\n\n\tpublic function store(Request \$request)\n\t{\n\t\t//store method\n\t}";
$txt .= "\n\n\tpublic function show(Request \$request)\n\t{\n\t\t//show method\n\t}";
$txt .= "\n\n\tpublic function update(Request \$request)\n\t{\n\t\t//update method\n\t}";
$txt .= "\n\n\tpublic function delete(Request \$request)\n\t{\n\t\t//delete method\n\t}";

$txt .= "\n}";

$open = fopen('App/Controller/'.$class_name.'.php','w');
fwrite($open, $txt);
fclose($open);

echo
Console::log($class_name, light_green).
Console::log("\tSUCCESS\n", green).
Console::log("location: ");

echo Console::color("App/Controller/{$class_name}.php\n", white, bg_blue);