# PHP Framework
[![GitHub issues](https://img.shields.io/github/issues/didaputraa/php-framework)](https://github.com/didaputraa/php-framework/issues)
[![GitHub stars](https://img.shields.io/github/stars/didaputraa/php-framework)](https://github.com/didaputraa/php-framework/stargazers)

Di buat dengan php native, ocok bagi yg mau terjun ke framework php atau lanjut ke expert programming

Fitur utama :
  - Model
  - View
  - Controller
  - Dsb...

Contoh penggunaan
```php
<?php
/*
  Contoh penggunaan Controller
*/
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
```
### Persiapan Server

Kebutuhan yg diperlukan

* PHP >= 7.4.x
* Apache 2.4.x
* MySQL

Untuk dokumentasi lengkap bisa hubungi saya