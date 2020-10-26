<?php
use System\Core\Template\Generator;

function view($viewArg = '',$dataArg = [])
{
	$file 	= "App/View/{$viewArg}";
	$content= '';
	$buffer	= '';
	
	if(file_exists($file.'.theme.php'))
	{
		$content = Generator::initCache($file.'.theme.php', Generator::templateInit($file.'.theme.php'), 'theme');
	}
	elseif(file_exists($file.'.php'))
	{
		$content = $file.'.php';
	}
	
	if(!empty($content))
	{
		if(!empty($dataArg))
		{
			$tmp = [];
			
			foreach($dataArg as $k => $v)
			{
				global ${$k};
				
				${$k} = $v;
				Generator::In($k);
			}
		}
		
		if(Generator::$content['status'] == 1)
		{
			require $content;
		}
		else
		{
			ob_start();
			require $content;
			
			$buffer = htmlspecialchars(ob_get_contents(), ENT_QUOTES);
			
			ob_end_clean();
		}
		
		if(!empty($dataArg))
		{
			foreach(array_keys($dataArg) as $k)
			{
				unset(${$k});
			}
		}
	}
	return htmlspecialchars_decode($buffer, ENT_QUOTES);
}