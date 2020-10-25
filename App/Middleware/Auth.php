<?php
namespace App\Middleware;

use System\Core\Http\Request;
use System\Core\Auth as Authentication;

class Auth
{
	public function handle(Request $request)
	{
		if(Auths::check())
		{
			return 1;
		}
		
		return redirect('login');
	}
}