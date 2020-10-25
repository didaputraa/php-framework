<?php
namespace System\Core\Engine;

class Console
{
	private static $color = [
	
	];
	
	
	
	public static function start()
	{
		define('bold', '1');    
		define('dim', '2');
        define('black', '0;30');
		define('dark_gray', '1;30');
        define('blue', '0;34');
		define('light_blue', '1;34');
        define('green', '0;32'); 
		define('light_green', '1;32');
        define('cyan', '0;36'); 
		define('light_cyan', '1;36');
        define('red', '0;31'); 
		define('light_red', '1;31');
        define('purple', '0;35');
		define('light_purple', '1;35');
        define('brown', '0;33');
		define('yellow', '1;33');
        define('light_gray', '0;37');
		define('white', '1;37');
        define('normal', '0;39');
		
		
        define('bg_black', '40');
		define('bg_red', '41');
        define('bg_green', '42');   
		define('bg_yellow', '43');
        define('bg_blue', '44');   
		define('bg_magenta', '45');
        define('bg_cyan', '46');   
		define('bg_light_gray', '47');
    
	}
	
	public static function log($txt = '', $color = normal)
	{
		return "\033[{$color}m {$txt}";
	}
	
	public static function color($txt = '', $font_color = '', $bg_color = '')
	{
		return "\033[{$font_color}m \033[{$bg_color}m{$txt}";
	}
}