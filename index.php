<?php
declare(strict_types=1);

session_start();
require_once __DIR__.'\PageNotFoundException.php';
require_once __DIR__.'\FileNotFoundException.php';
require_once __DIR__.'\controllers\BaseController.php';
require_once __DIR__.'\controllers\LoginController.php';
require_once __DIR__.'\controllers\MainController.php';
require_once __DIR__.'\controllers\SettingsFragmentController.php';
require_once __DIR__.'\Router.php';
require_once __DIR__.'\RequestHandler.php';
require_once __DIR__.'\database\DB.php';
require_once __DIR__.'\models\User.php';
require_once __DIR__.'\models\Warehouse.php';
require_once __DIR__.'\models\Log.php';

$router = new Router();
$router->get('/',[LoginController::class,'index']);
$router->post('/login',[LoginController::class,'login']);
$router->get('/logout',[LoginController::class,'logout']);
$router->get('/warehouse',[MainController::class,'index']);
$router->get('/warehouse/settings',[SettingsFragmentController::class,'index']);
$router->get('/warehouse/settings/edit_user',[SettingsFragmentController::class,'editUser']);
$router->get('/warehouse/settings/add_user',[SettingsFragmentController::class,'addUser']);
$router->post('/warehouse/settings/add_user',[SettingsFragmentController::class,'submitUser']);
$router->post('/warehouse/settings/edit_user',[SettingsFragmentController::class,'submitUser']);
$router->post('/warehouse/settings',[SettingsFragmentController::class,'editCurrentUser']);
$router->get('/warehouse/settings/add_warehouse',[SettingsFragmentController::class,'editWarehouse']);
$router->get('/warehouse/settings/edit_warehouse',[SettingsFragmentController::class,'editWarehouse']);
$router->get('/warehouse/logs',[MainController::class,'logs']);
$router->get('/warehouse/products',[MainController::class,'products']);

$requestHandler = new RequestHandler($router);
$requestHandler->handleRequest(strtolower($_SERVER['REQUEST_METHOD']),$_SERVER['REQUEST_URI']);

DB::disconnectIfConnected();