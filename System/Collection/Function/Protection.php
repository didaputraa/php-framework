<?php
use System\Core\Session\Session;

function _getCsrf()
{
	return Session::get('__CSRF-TOKEN__');
}

function _csrfField()
{
	return '<input type="hidden" name="_csrf-token" value="'._getCsrf().'" />';
}

function _csrfMethod($type = 'POST')
{
	return '<input type="hidden" name="_csrf-method" value="'.strtoupper($type).'" />';
}