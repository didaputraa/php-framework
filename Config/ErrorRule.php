<?php
namespace Config;

class ErrorRule
{
    private static $error = [
        
        'production'    => 'Halaman ini perlu diperbaiki',

        'maintenance'   => 'Website sedang maintenance'
    ];
 
    public static function getMessage($code)
    {
        return self::$error[$code];
    }

}