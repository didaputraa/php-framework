# PHP Framework
[![GitHub issues](https://img.shields.io/github/issues/didaputraa/php-framework)](https://github.com/didaputraa/php-framework/issues)
[![GitHub stars](https://img.shields.io/github/stars/didaputraa/php-framework)](https://github.com/didaputraa/php-framework/stargazers)

Di buat dengan php native, cocok bagi yg mau terjun ke framework php atau lanjut ke expert programming

Fitur utama :
  - Model
  - View
  - Controller
  - Dsb...

Contoh penggunaan
```bash
php engine controller:HomeController
```
```php
<?php
/*
  App/Controller/HomeController.php
  Contoh penggunaan Controller
*/
namespace App\Controller;

use System\Controllers\Controller;
use System\Core\Http\Request;

class HomeController extends Controller
{
	public function index(Request $request)
	{
		return view('content',['message' => 'PHP Framework']);
	}
}
```
```php
<!-- App/View/content.theme.php -->
<h1>{{ $message  }}<?h1>
```
### Persiapan Server

Kebutuhan yg diperlukan

* PHP >= 7.4.x
* Apache 2.4.x
* MySQL

Untuk dokumentasi lengkap kunjungi https://didaputraa.github.io/php-framework-docs/