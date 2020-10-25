<?php
namespace System\Core\Protection;

use \System\Core\Http\Request;

trait ValidateRequest
{
	private static $errorMessage 		= null;


	final public function buildError()
	{
		foreach(self::$errorMessage as $field => $warning)
		{
			$_SESSION['err__'.$field.'__enderr'] = $warning;
		}
	}

	final public function validate($rule = [], $err = [], $replacement = [])
	{
		if(!empty($rule))
		{
			$get	= new Request;
			$fields = array_keys($rule);
			$valid  = array_values($rule);
			$where  = [];
			
			foreach($fields as $no => $field)
			{
				$whois = self::validate_rules__($valid[$no], $field, $get->input($field), $replacement);
				
				if(!empty($whois))
				{
					$k = array_keys($whois);
					$v = array_values($whois);
					
					$where[$k[0]] = $v[0];
				}
			}
			
			if(!empty($err))
			{
				foreach($err as $fieldRule => $message)
				{
					$pecahan = explode('.', $fieldRule);

					$where[$pecahan[0]][$pecahan[1]] = $message;
				}
			}
			
			if(!empty($where))
			{
				self::$errorMessage = $where;

				return true;
			}

			return false;
		}
		
		return false;
	}
	
	private static function unset_errCycle($name)
	{
		$sess = 'err__'.$name.'__enderr';

		if(isset($_SESSION[$sess]))
		{
			unset($_SESSION[$sess]);
		}
	}

	final private function validate_rules__($rules = '', $name = '', $inputan = '', $replacement = [])
	{
		if(!empty($rules))
		{
			$error 		= [];
			$rule  		= '';
			$pecah 		= explode('|', $rules);
			$name_field = isset($replacement[$name])? $replacement[$name] : $name;

			foreach($pecah as $check)
			{
				$match = explode(':', $check);

				self::unset_errCycle($name);
				
				if($match[0] == 'required' && strlen($inputan) == 0 && $inputan == '')
				{
					$error[$name]['required'] = "required value in the {$name_field} field";
					break;
				}
				
				if($match[0] == 'unique')
				{
					$unik = explode(',', $match[1]);
					
					if(in_array($inputan, $unik))
					{
						$error[$name]['unique'] = "unique value in the {$name_field} field is {$match[1]}";
						break;
					}
				}
				
				if($match[0] == 'min')
				{
					if(is_numeric($inputan))
					{
						if((int)$inputan < $match[1])
						{
							$error[$name]['min'] = "Minimum value in the {$name_field} field is {$match[1]}";
							break;
						}
					}
					else
					{
						if(strlen($inputan) < $match[1])
						{
							$error[$name]['min'] = "Minimum value in the {$name_field} field is {$match[1]}";
							break;
						}
					}
				}
				
				if($match[0] == 'max')
				{
					if(is_numeric($inputan))
					{
						if((int)$inputan > $match[1])
						{
							$error[$name]['max'] = "Maximum value in the {$name_field} field is {$match[1]}";
							break;
						}
					}
					else
					{
						if(strlen($inputan) > $match[1])
						{
							$error[$name]['max'] = "Maximum value in the {$name_field} field is {$match[1]}";
							break;
						}
					}
				}

				if($match[0] == 'same')
				{
					if($inputan != Request::input($match[1]))
					{
						$notMatch = isset($replacement[$match[1]])? $replacement[$match[1]] : $match[1];

						$error[$name]['same'] = "This value in the {$name_field} don't match {$notMatch}";
						break;
					}
				}
			}
			
			return $error;
		}
	}
}