<?php
include 'System/autoload.php';


$dir = realpath('./');
$path= str_replace("\\","/", $dir);


System\autoload::loader();

System\Core\ErrHandle\Error::analize($path.'/');

System\Collection\Functions::initialize();



use System\Route\RouteManager as route;
use System\Core\Session\SessionManager as sess;
use Config\App;


date_default_timezone_set(App::getTimezone());

if(route::checkEmptyOrUpdate())
{
	require 'Route/url.php';
	
	route::build();
}


if(route::routeExists())
{
	$rute = route::getRoutes();
	$sess = $path.'/Storage/sess';
	
	\System\Core\ErrHandle\Error::isMaintenance();


	sess::setUP($sess);
	sess::run($sess);
	

	if(file_exists($rute->classes->fullPath.'/'.$rute->classes->class.'.php'))
	{
		$className = ($rute->classes->namespace.'\\'.$rute->classes->class);
		$reflect   = new ReflectionClass($className);
		
		
		if($reflect->hasMethod($rute->classes->method))
		{
			$method	= $reflect->getMethod($rute->classes->method);
			
			if($method->isPublic() && !$method->isStatic())
			{
				$provider = new System\Core\Protection\ServiceProvider;
				
				$provider->invokeProvider();
				$provider->invokeMiddleware($rute);
				
				$_generators = new System\Core\Template\Generator;
				

				if($method->getNumberOfParameters() == 0)
				{
					$respon = $method->invoke(new $className);
					
					
					if($_generators::$content['status'] == 1)
					{
						//with template Engine
						echo $_generators::phpSectionExtends_show();
					}
					else
					{
						//without template Engine
						echo $respon;
					}
				}
				else
				{
					$arguments = $method->getParameters();
					$params    = [];
					
					
					foreach($arguments as $number => $p)
					{
						if(isset($p->getClass()->name))
						{
							$classTmp = $p->getClass()->name;
							
							$params[$number] = new $classTmp;
						}
						else
						{
							if($p->isDefaultValueAvailable())
							{
								$params[$number] = $p->getDefaultValue();
							}
							else
							{
								\System\Core\ErrHandle\Error::errorCode('','Parameter is not default value',$rute->classes->fullPath.'/'.$rute->classes->class.'.php', $method->getStartLine());
							}
						}
					}
					
					
					$respon = $method->invokeArgs(new $className, $params);
					
					if($_generators::$content['status'] == 1)
					{
						//with template Engine
						echo $_generators::phpSectionExtends_show();
					}
					else
					{
						//without template Engine
						echo $respon;
					}
					
				}
			}
			else
			{
				\System\Core\ErrHandle\Error::errorCode('','Harap menggunakan public function jangan static/lainnya',$method->getFileName(), $method->getStartLine());
			}
		}
		else
		{
			\System\Core\ErrHandle\Error::errorCode('','Method tidak ditemukan',$rute->classes->fullPath.'/'.$rute->classes->class.'.php', 0);
		}
	}
	else
	{
		\System\Core\ErrHandle\Error::errorCode('','Controller tidak ditemukan','', 0);
	}
}
else
{
	\System\Core\ErrHandle\Error::error404();
}