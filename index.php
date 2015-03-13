<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Elite\Bootstrap\SlimBootstrap;
use Slim\Slim;
use Slim\Views\Twig as Twig;

date_default_timezone_set('UTC');

error_reporting(E_ALL); // for development only.
ini_set("display_errors", 1); // for development only
define('SCRIPT_DEBUG', true);

require 'vendor/autoload.php';
require '../vendor/autoload.php';

// middleware (TODO: add an autoloader, and place inside /app/middleware)
require 'middleware/navigation.php';
require 'middleware/session.php';

// bootstrap
require 'app/core/EliteBootstrap.php';

// Require all models (TODO: add autoloader and place inside /app/extras)
foreach (glob('models/*.php') as $model) {
        require_once $model;
}

// Eloquent Models (TODO: add to autoloader)
foreach (glob('app/models/*.php') as $ormmodel) {
	require $ormmodel;
}

// Config file.
$config = require 'app/core/global.php';

// Config slim|elitewars|twig.
$app = new Slim($config['slim']);
$bootstrap = new SlimBootstrap($app, new Session, $config, new Capsule);
$app = $bootstrap->bootstrap();


// NOTE: Deprecated.
$isLoggedIn = function() {
    return function () {
        $app = \Slim\Slim::getInstance();
        Session::init();
    	if (!Session::isLoggedIn()) {
            $app->redirect('login');
        }
    };
};


// Require all routes
foreach(glob('routes/*.php') as $router) {
    require_once $router;
}

// Run the app|game
$app->run();
