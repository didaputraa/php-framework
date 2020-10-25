<?php
namespace App\Controller;

use System\Controllers\Controller;
use System\Core\Http\Request;


class HomeController extends Controller
{
	public function index(Request $request)
	{
		return view('content');
	}
}