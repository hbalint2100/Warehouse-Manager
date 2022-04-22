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
        $this->setTitle('Home - Warehouse Manager');
        $this->setDescription('Home page for warehouse database manager');
        $this->setUpMainView();
        $this->show();
    }

    public function settings()
    {
        $this->checkLogin();
        $this->setTitle('Settings - Warehouse Manager');
        $this->setDescription('Settings page for warehouse database manager');
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\SettingsFragment.php');
        $user = User::getUserByID(LoginController::getUserID());
        if(!is_null($user))
        {
            $this->fragmentArray['username'] = $user->getUserName();
            $this->fragmentArray['privilegelevel'] = $user->getPriviligeLevel();
        }
        if(self::showOtherUsers())
        {
            $users = User::getAllUsers();
            if(!is_null($users))
            {
                $this->fragmentArray['users'] = array();
                foreach($users as $user)
                {
                    if($user->getUserId()!==LoginController::getUserID())
                    {
                        array_push($this->fragmentArray['users'],$user);
                    }
                }
            }
        }
        $warehouses = Warehouse::getAllWarehouses();
        if(!is_null($warehouses))
        {
            $this->fragmentArray['warehouses'] = array();
            foreach($warehouses as $warehouse)
            {
                array_push($this->fragmentArray['warehouses'],$warehouse);
            }
        }
        $this->show();
    }

    public function editUser()
    {
        $this->checkLogin();
        $this->setTitle('Edit user - Warehouse Manager');
        $this->setDescription('Edit user subpage of settings');
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditUserFragment.php');
        $this->fragmentArray['title'] = "Edit user";
        $this->show();
    }

    public function addUser()
    {
        $this->checkLogin();
        $this->setTitle('New user - Warehouse Manager');
        $this->setDescription('New user subpage of settings');
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditUserFragment.php');
        $this->fragmentArray['title'] = "New user";
        $this->show();
    }

    public function  submitUser()
    {
        $this->checkLogin();
        $this->setTitle('New user - Warehouse Manager');
        $this->setDescription('New user subpage of settings');
        $this->setUpMainView();
        $this->show();
    }

    public function editWarehouse()
    {
        $this->checkLogin();
        $this->setTitle('Edit warehouse - Warehouse Manager');
        $this->setDescription('Edit warehouse subpage of settings');
        $this->setUpMainView();
        $this->show();
    }

    public function logs()
    {
        $this->checkLogin();
        if(!LoginController::isAdmin())
        {
            header("Location:/warehouse/",true,303);
            exit;
        }
        $this->setTitle('Logs - Warehouse Manager');
        $this->setDescription('Log page for warehouse database manager');
        $this->setUpMainView();
        $this->show();
    }

    public function products()
    {
        $this->checkLogin();
        $this->setTitle('Products - Warehouse Manager');
        $this->setDescription('Page for products management');
        $this->setUpMainView();
        $this->show();
    }

    private function setUpMainView()
    {
        $this->setBodyPath(parent::VIEWS.'\MainView\Main.php');
        $this->setStyleSheetPath('/../views/MainView/Main.css');
        $this->setNavbarPath(parent::VIEWS.'\NavBar\Navbar.php');
        $this->setFavIconPath('./favicon.ico');
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

    public function showOtherUsers()
    {
        return LoginController::isAdmin();
    }

    public function logsEnabled()
    {
        return LoginController::isAdmin();
    }
    public function editWarehouses()
    {
        return LoginController::isAdmin();
    }
}