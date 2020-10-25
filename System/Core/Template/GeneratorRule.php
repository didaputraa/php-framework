<?php
namespace System\Core\Template;

class GeneratorRule
{
	protected static $pattern = 
	[
		0 => '/{{\s(.+?)\s}}/',
		1 => '/{!!\s(.+?)\s!!}/',
		
		3 => '/@if\((.+?)\)\r/',
		4 => '/@elseif\((.+?)\)\r/',
		5 => '/@(else)\r/',
		6 => '/@(endif)\r/',
		
		7 => '/@import\((.+?)\)/',
		
		8 => '/@switch\((.+?)\)(\s*)@case\((.+?)\)\r/',
		9 => '/@case\((.+?)\)\r/',
		10 => '/@(break)\r/',
		11 => '/@(default)\r/',
		12 => '/@(endswitch)\r/',
		
		13 => '/@foreach\((.+?)\)\r/',
		14 => '/@endforeach\r/',
		
		15 => '/@for\((.+?)\)\r/',
		16 => '/@endfor\r/',
		
		17 => '/@while\((.+?)\)\r/',
		18 => '/@(endwhile)\r/',
		
		19 => '/@extends\((.+?)\)\r/',
		20 => '/@section\((.+?)\,(.+?)\)\r/',
		21 => '/@section\((.+?)\)\r/',
		22 => '/@endsection\r/',
		23 => '/@yields\((.+?)\)/',

		24 => '/@error\((.+?)\)\r/',
		25 => '/@enderror\r/'
	];
	
	protected static $replacement = 
	[
		0 => "<?php echo htmlspecialchars($1,ENT_QUOTES); ?>",
		1 => "<?php echo $1; ?>",
		
		3 => "<?php if($1): ?>",
		4 => '<?php elseif($1): ?>',
		5 => '<?php $1: ?>',
		6 => '<?php $1; ?>',
		
		7 => "<?php System\Core\Template\Generator::phpImportTemplate($1); ?>",
		
		8 => "<?php switch($1):$2case $3: ?>",
		9 => '<?php case $1: ?>',
		10 => '<?php $1; ?>',
		11 => '<?php default: ?>',
		12 => '<?php endswitch ?>',
		
		13 => '<?php foreach($1): ?>',
		14 => '<?php endforeach; ?>',
		
		15 => '<?php for($1): ?>',
		16 => '<?php endfor; ?>',
		
		17 => '<?php while($1): ?>',
		18 => '<?php $1; ?>',
		
		19 => '<?php System\Core\Template\Generator::phpSectionExtends($1); ?>',
		20 => '<?php System\Core\Template\Generator::phpSectionStart($1,$2); ?>',
		21 => '<?php System\Core\Template\Generator::phpSectionStart($1); ?>',
		22 => '<?php System\Core\Template\Generator::phpSectionEnd(); ?>',
		23 => '<?php System\Core\Template\Generator::phpSection_show($1); ?>',

		24 => '<?php $__object_item_= $1; $__phperr__ = System\Core\Template\Generator::phpErrorValidate($1); $message = $__phperr__[\'val\']; ?>',
		25 => '<?php System\Core\Template\Generator::endphpErrorValidation($__object_item_); unset($message, $__object_item_, $__phperr__); ?>',
	];
	
	private function compileEcho()
	{
		///$result = preg_replace('/a/','','');
	}
	
	private function compileControlOutput()
	{
		
	}
	
	private function compileTemplate()
	{
		
	}
}