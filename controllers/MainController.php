<?php

declare(strict_types=1);
class MainController extends BaseController
{
    private ?string $navbarPath = null;
    private ?string $fragmentPath = null;
    protected ?array $fragmentArray = null;

    public function index()
    {
        $this->checkLogin();
        $this->setTitle('Home - Warehouse Manager');
        $this->setDescription('Home page for warehouse database manager');
        $this->setUpMainView();
        $this->show();
    }

    protected function setUpMainView()
    {
        $this->setBodyPath(parent::VIEWS.'\MainView\Main.php');
        $this->setStyleSheetPath('/../views/MainView/Main.css');
        $this->setNavbarPath(parent::VIEWS.'\NavBar\Navbar.php');
        $this->setFavIconPath('/../favicon.ico');
    }

    protected function show()
    {
        $this->checkLogin();
        parent::show();
    }

    protected function checkLogin()
    {
        if(!LoginController::loggedIn())
        {
            header("Location:/",true,303);
            exit;
        }
    }

    public function getNavbarPath()
    {
        return $this->navbarPath;
    }

    private function setNavbarPath(string $i_navbarPath)
    {
        if(!file_exists($i_navbarPath))
        {
            throw new FileNotFoundException($i_navbarPath.' is missing!');
        }
        $this->navbarPath = $i_navbarPath;
    }

    protected function setFragmentPath(string $i_fragmentPath)
    {
        if(!file_exists($i_fragmentPath))
        {
            throw new FileNotFoundException($i_fragmentPath.' is missing!');
        }
        $this->fragmentPath = $i_fragmentPath;
    }

    public function getFragmentPath()
    {
        return $this->fragmentPath;
    }

    public function getFragmentArray()
    {
        return $this->fragmentArray;
    }

    public function logsEnabled()
    {
        return LoginController::isAdmin();
    }
}