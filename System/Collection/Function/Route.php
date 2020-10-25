<?php
use System\Route\RouteManager as route;

function routeUrl()
{
	return route::getRoutes();
}

function routeUrlActive()
{
	$rute = route::getRoutes();
	
	return $rute->urlActive == '/'? '/' : substr($rute->urlActive,1);
}

function redirect($location = '')
{
	if($location != '')
	{
		return header('location:'.base_url($location));
	}
	else
	{
		return new class
		{
			function away($url = '')
			{
				if(!empty($url))
				{
					return header('location: '.$url);
				}
			}
		};
	}
}

function routeName()
{
	
}