<?php
namespace System\Core\ErrHandle;

class ErrorContent
{
	public function analize($txt)
	{
		$content = htmlspecialchars($txt, ENT_QUOTES);
		
		//$content = self::compileHtml($content);
		$content = self::compilePHP($content);
		
		return $content;
	}
	
	public function compileHtml($txt = '')
	{
		return preg_replace([
		
		],[
		
		],$txt);
	}
	
	public function compilePHP($txt = '')
	{
		$opt = 'trait|abstract|interface|final|public|private|protected|static|function';
		$opt2= 'if|else|elseif|endif|while|case|break|default|continue|for|foreach|echo|include|include_once|require|require_once|endforeach|endif|endfor';
		
		$txt = preg_replace(['/(&lt;\?php)/','/(\?&gt;)/'],'<span style="color:#ff7c7c">$1</span>', $txt); //<?php
		
		$txt = preg_replace('/(&quot;.+?&quot;)/','<span style="color:#debe97">$1</span>', $txt); //'string'
		
		$txt = preg_replace('/(\$\w+)/','<span style="color:#b7a9a3">$1</span>', $txt); //$variable
		
		$txt = preg_replace('/(namespace|use)(\s.+?);/','<span class="err-php_e">$1</span>$2;', $txt); //namespace example/class;
		
		$txt = preg_replace('/(class\s)/','<span class="err-php_e">$1</span>', $txt); //class 
		
		$txt = preg_replace('/(return)(\s.+);/', '<span class="err-php_e">$1</span>$2;', $txt); //return value;
		
		$txt = preg_replace('/([a-zA-Z0-9_]+)(\(.+?\))/', '<span class="err-php_e">$1</span>$2', $txt); //function($arg)
		
		$txt = preg_replace('/([a-zA-Z0-9_]+)(\(.+?\));/', '<span class="err-php_e">$1</span>$2;', $txt); //function($arg);
		
		$txt = preg_replace('/(&#039;.+?&#039;)/','<span style="color:#debe97">$1</span>', $txt); //'string'
		
		$txt = preg_replace('/([a-zA-Z0-9_]+)(\(\))/', '<span class="err-php_e">$1</span>$2', $txt); //function()
		
		$txt = preg_replace("/({$opt})/", '<span class="err-php_e">$1</span>', $txt); //operator
		
		
		$txt = preg_replace("/({$opt2})/",'<span class="err-php_e">$1</span>', $txt); //<?php
		
		return $txt;
	}
}