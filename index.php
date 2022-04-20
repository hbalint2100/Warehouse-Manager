<?php
declare(strict_types=1);

session_start();
require_once __DIR__.'\PageNotFoundException.php';
require_once __DIR__.'\FileNotFoundException.php';
require_once __DIR__.'\controllers\BaseController.php';
require_once __DIR__.'\controllers\LoginController.php';
require_once __DIR__.'\controllers\MainController.php';
require_once __DIR__.'\Router.php';
require_once __DIR__.'\RequestHandler.php';
require_once __DIR__.'\database\DB.php';
require_once __DIR__.'\models\User.php';

$router = new Router();
$router->get('/',[LoginController::class,'index']);
$router->post('/login',[LoginController::class,'login']);
$router->get('/logout',[LoginController::class,'logout']);
$router->get('/warehouse',[MainController::class,'index']);

$requestHandler = new RequestHandler($router);
$requestHandler->handleRequest(strtolower($_SERVER['REQUEST_METHOD']),$_SERVER['REQUEST_URI']);

DB::disconnectIfConnected();