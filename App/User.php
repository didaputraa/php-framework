<?php
namespace App;

use System\Database\Model;

class User extends Model
{
	protected static $table		= 'user';
	protected static $primary	= 'username';
	protected static $fillable	= ["username", "password"];
}
