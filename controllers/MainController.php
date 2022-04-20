<?php

declare(strict_types=1);
class MainController extends BaseController
{
    private $navbarPath;

    public function index()
    {
        if(LoginController::loggedIn())
        {
            $this->setTitle('Home - Warehouse Manager');
            $this->setDescription('Home page for warehouse database management');
            $this->setBodyPath(parent::VIEWS.'\MainView\Main.php');
            $this->setStyleSheetPath('../views/MainView/Main.css');
            $this->setNavbarPath(parent::VIEWS.'\NavBar\Navbar.php');
            $this->show();
        }
        else
        {
            header("Location:/",true,303);
        }
    }

    public function getNavbarPath()
    {
        return $this->navbarPath;
    }

    private function setNavbarPath(string $i_navbarPath)
    {
        $this->navbarPath = $i_navbarPath;
    }
}