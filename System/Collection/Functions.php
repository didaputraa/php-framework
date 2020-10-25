<?php
namespace System\Collection;

class Functions
{
	public function initialize()
	{
		foreach(['View', 'Asset', 'Request', 'Response', 'Route', 'Protection'] as $i)
		{
			require_once "Function/{$i}.php";
		}
	}
}