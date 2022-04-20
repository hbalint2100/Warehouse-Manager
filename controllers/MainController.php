<?php

declare(strict_types=1);
class MainController extends BaseController
{
    private ?string $navbarPath = null;
    private ?string $fragmentPath = null;
    private ?array $fragmentArray = null;

    public function index()
    {
        $this->checkLogin();
        $this->setUpMainView();
        $this->show();
    }

    public function settings()
    {
        $this->checkLogin();
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\SettingsFragment.php');
        $user = User::getUserByID(LoginController::getUserID());
        if(!is_null($user))
        {
            $this->fragmentArray['username'] = $user->getUserName();
            $this->fragmentArray['privilegelevel'] = $user->getPriviligeLevel()==PrivilegeLevels::ADMIN? 'admin' : 'user';
        }
        $this->show();
    }

    public function logs()
    {
        $this->checkLogin();
        $this->setUpMainView();
        $this->show();
    }

    public function products()
    {
        $this->checkLogin();
        $this->setUpMainView();
        $this->show();
    }

    private function setUpMainView()
    {
        $this->setTitle('Home - Warehouse Manager');
        $this->setDescription('Home page for warehouse database manager');
        $this->setBodyPath(parent::VIEWS.'\MainView\Main.php');
        $this->setStyleSheetPath('../views/MainView/Main.css');
        $this->setNavbarPath(parent::VIEWS.'\NavBar\Navbar.php');
    }

    private function checkLogin()
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

    private function setFragmentPath(string $i_fragmentPath)
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