<?php

// TODO: Create a bootstrap for this page. Also create a config array..

// During development.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'vendor/autoload.php';
require '../vendor/autoload.php';


$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig()
	)
);

// Configure twig options.
$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__) . '/cache'
);

// Config twig extensions.
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);




// Require all models
foreach (glob('models/*.php') as $model) {
    require_once $model;
}

// Init our session (todo: create middleware for this.)
Session::init();

// Add an autoloader to the composer file? For the models used.
// Also, use the dependence injection container, to allow for lazy loading?
$db = new Database();
$auth = new Auth($db);


use Illuminate\Database\Capsule\Manager as Capsule;


// Database information.
$settings = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'db',
    'username' => 'user',
    'password' => 'password',
    'collation' => 'utf8_general_ci',
    'charset' => 'utf8',
    'prefix' => ''
);


// Needed to have access to models for classes.
$capsule = new Capsule;
$capsule->addConnection($settings);
$capsule->bootEloquent();



// Bootstrap Eloquent ORM to slim.
$app->container->singleton('capsule', function() use ($settings) {
    
    $capsule = new Illuminate\Database\Capsule\Manager;
    $capsule->setFetchMode(PDO::FETCH_OBJ); // This is causing problems.
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    
    return $capsule->getConnection();
    
});



// Add custom middleware. (include all these files. with foreach(glob('middleware/*.php'))
require 'middleware/navigation.php';
$app->add(new Slim\Middleware\Navigation($auth, $db));


// TODO: Create a middleware extension class for this?
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

// Run the app.
$app->run();
