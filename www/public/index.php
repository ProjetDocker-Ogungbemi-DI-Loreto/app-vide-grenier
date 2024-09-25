<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

session_start();

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Users', 'action' => 'login']);
$router->add('register', ['controller' => 'Users', 'action' => 'register']);
$router->add('logout', ['controller' => 'Users', 'action' => 'logout', 'private' => true]);
$router->add('account', ['controller' => 'Users', 'action' => 'account', 'private' => true]);
$router->add('product', ['controller' => 'Product', 'action' => 'index', 'private' => true]);
$router->add('product/{id:\d+}', ['controller' => 'Product', 'action' => 'show']);
$router->add('{controller}/{action}');

// Nouvelle route pour la recherche d'articles
$router->add('api/search', ['controller' => 'Api', 'action' => 'SearchAction']);

// Routes pour les fonctionnalités "À la une" et "Autour de moi"
$router->add('api/featured', ['controller' => 'Api', 'action' => 'FeaturedAction']);
$router->add('api/nearby', ['controller' => 'Api', 'action' => 'NearbyAction']);

/*
 * Gestion des erreurs dans le routing
 */
try {
    $router->dispatch($_SERVER['QUERY_STRING']);
} catch(Exception $e){
    switch($e->getMessage()){
        case 'You must be logged in':
            header('Location: /login');
            break;
    }
}
