<?php

use Slim\Views\Twig as Twig;


if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(dirname(__DIR__)));
}


return array(

    // Slim config
    'slim' => array(
        'debug' => true,
        'view' => new Twig, // Define 
        'cookies.encrypt' => true,
        'cookies.secret_key' => 'iLuVmUsHrOoMz',
        'cookies.cipher' => MCRYPT_RIJNDAEL_256,
        'cookies.cipher_mode' => MCRYPT_MODE_CBC,
    ),
    
    // Twig config
    'twig' => array(
        'environment' => array(
            'charset' => 'utf-8',
            'cache' => '../templates/cache',
            'auto_reload' => false,
            'strict_variables' => true,
            'autoescape' => true,
            'debug' => false,
        ),
    ),

    // Database config
    'database' => array(
	'driver' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'wwwelite_rpg',
	'username' => 'wwwelite_rpguser',
	'password' => '+4c%7kf1S6P+',
	'collation' => 'utf8_general_ci',
	'charset' => 'utf8',
	'prefix' => ''
    )
);
