<?php
declare(strict_types=1);

session_start();
require_once __DIR__.'\PageNotFoundException.php';
require_once __DIR__.'\FileNotFoundException.php';
require_once __DIR__.'\controllers\BaseController.php';
require_once __DIR__.'\controllers\LoginController.php';
require_once __DIR__.'\controllers\MainController.php';
require_once __DIR__.'\controllers\_404Controller.php';
require_once __DIR__.'\controllers\SettingsFragmentController.php';
require_once __DIR__.'\controllers\LogsFragmentController.php';
require_once __DIR__.'\controllers\ProductsFragmentController.php';
require_once __DIR__.'\Router.php';
require_once __DIR__.'\RequestHandler.php';
require_once __DIR__.'\database\DB.php';
require_once __DIR__.'\models\User.php';
require_once __DIR__.'\models\Warehouse.php';
require_once __DIR__.'\models\Log.php';
require_once __DIR__.'\models\Product.php';
require_once __DIR__.'\models\Stock.php';

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
$router->post('/warehouse/settings/add_warehouse',[SettingsFragmentController::class,'submitWarehouse']);
$router->post('/warehouse/settings/edit_warehouse',[SettingsFragmentController::class,'submitWarehouse']);
$router->get('/warehouse/logs',[LogsFragmentController::class,'index']);
$router->get('/warehouse/products',[ProductsFragmentController::class,'index']);
$router->get('/warehouse/products/add_product',[ProductsFragmentController::class,'addProduct']);
$router->get('/warehouse/products/edit_product',[ProductsFragmentController::class,'editProduct']);

$requestHandler = new RequestHandler($router);
$requestHandler->handleRequest(strtolower($_SERVER['REQUEST_METHOD']),$_SERVER['REQUEST_URI']);

DB::disconnectIfConnected();