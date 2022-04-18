<?php

declare(strict_types=1);
class MainController extends BaseController
{
    public function index()
    {
        if(LoginController::loggedIn())
        {
            $this->setTitle('Home - Warehouse Manager');
            $this->setDescription('Home page for warehouse database management');
            $this->setBodyPath(parent::VIEWS.'\MainView\Main.php');
            $this->setStyleSheetPath('../views/MainView/Main.css');
            $this->show();
        }
        else
        {
            header("Location:/",true,303);
        }
    }
}