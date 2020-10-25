<?php
use System\Core\Http\Request as url;
use Config\App;

function asset($file = '')
{
	$app 	= App::getSite();
	$ret 	= "{$app->protocol}://{$app->domain}:{$app->port}".url::UrlBeforeRender().'Asset';
	
	if(empty($file))
	{
		return $ret;
	}
	else
	{
		return $ret.'/'.$file;
	}
}

function base_url($location = '')
{
	if(!empty($location))
	{
		$app 		= App::getSite();
		$before		= url::UrlBeforeRender();
		$location 	= empty($location)? '' : $location;
		
		$url 		= "{$app->protocol}://{$app->domain}:{$app->port}{$before}{$location}";
		
		return $url;
	}
}