<?php
use System\Route\RouteManager as route;


route::get('/', 'HomeController@index')->name('home');