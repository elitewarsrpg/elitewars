<?php

/**
 * Routes for -
 * - Index
 * - Register
 * - Login
 * - Logout
 */

$app->get('/', function() use ($app) {	
    $app->render('index.twig');
});

$app->get('/index', function() use ($app) {
    $app->render('index.twig');
});


// Note: This is getting updated, due to the switch to eloquent orm.
$app->get('/register', function() use ($app) {
    $app->render('register.twig');
});
$app->post('/register', function() use ($app, $db) {
    $username = $app->request()->post('username');
    $password = $app->request()->post('password');
    $vpassword = $app->request()->post('vpassword');
    $email = $app->request()->post('email');

    // Registration process.
    $auth = new Auth($db);
    if ($auth->register($username, $email, $password, $vpassword)) {
        $app->redirect('login');
    }
    else {
        echo 'register fail';
    }  
    
    // BASIC Example using the eloquent ORM upgrade. (note basic)
    $user = new User;
    $user->username = $username;
    $user->password = password_hash($password, PASSWORD_DEFAULT);
    $user->email = $email;
    $user->save();
});

// Note: This is getting updated, due to the switch to eloquent orm.
$app->map('/login', function() use ($app, $db) {

    // login form has been submitted.
    if ($app->request()->isPost()) {
        $username = $app->request()->post('username');
        $password = $app->request()->post('password');
    
        $auth = new Auth($db);
        if ($auth->login($username, $password)) {
            $app->redirect('members');
        } else {
            $app->flashNow('error', 'Login failed. Please try again.'); // Login error.
        }
    }
    $app->render('login.twig');
})->via('GET','POST')->name('login');


// Logout
$app->get('/logout', function() use ($app) {
	Session::init();
	Session::destroy();
	$app->redirect('/login');
});
