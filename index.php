<?php

// TODO: Create a bootstrap for this page. Also create a config array..

// During development.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'vendor/autoload.php'; // Loads slim and twig composer files.
require '../vendor/autoload.php'; // Loads illuminate/database composer files.

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
$db = new Database(); // Deprecated, just need redo the models that still use Database\PDO.
$auth = new Auth($db); // Soon to be implementing the eloquent orm here, $db will be deprecated.


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

// To extend Illuminate\Database\Eloquent\Models and have access to that class - this is needed.
$capsule = new Capsule;
$capsule->addConnection($settings);
$capsule->bootEloquent();

// Bootstrap Eloquent ORM to slim.
$app->container->singleton('capsule', function() use ($settings) {
    
    $capsule = new Illuminate\Database\Capsule\Manager;
    $capsule->setFetchMode(PDO::FETCH_OBJ);
    $capsule->addConnection($settings);
    $capsule->bootEloquent();
    
    return $capsule->getConnection();
    
});


// Add custom middleware.
require 'middleware/navigation.php';
$app->add(new Slim\Middleware\Navigation($auth, $db));


// For now, this checks if a user is logged in, and if not redirects to the user page.
// Middleware will be added for this.
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
