<?php

class SettingsFragmentController extends MainController
{
    public function index()
    {
        $this->checkLogin();
        if(isset($_GET['deleteuser'])&&$_GET['deleteuser']=='true')
        {
            //Deleteing current user account
            (new Log("Deleted user: ".User::getUserByID(LoginController::getUserID())->getUserName(),LoginController::getUserID()))->save();
            User::deleteUserByID(LoginController::getUserID());
            header('Location: /logout',true,303);
            exit;
        }
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
        if($this->showOtherUsers())
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

    public function showOtherUsers()
    {
        return LoginController::isAdmin();
    }

    public function editWarehouses()
    {
        return LoginController::isAdmin();
    }

    private function checkUsername($i_username)
    {
        //Must contain 4 chars, at least one letter
        $pattern = "/^(?=.*[A-Za-z])[A-Za-z\d]{4,}$/";
        return preg_match($pattern,$i_username);
    }

    private function checkPass($i_pass)
    {
        //Must contain 8 chars, at least one letter, at least one number
        $pattern = "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/";
        return preg_match($pattern,$i_pass);
    }

    public function editWarehouse()
    {
        if(!LoginController::isAdmin())
        {
            header('Location:/warehouse/settings',true,303);
            exit;
        }
        $this->setDescription('Edit warehouse subpage of settings');
        $this->setUpMainView();
        if($this->getPath()=='/warehouse/settings/add_warehouse')
        {
            $this->setTitle('New warehouse - Warehouse Manager');
            $this->fragmentArray['title'] = 'New warehouse';
        }
        else if($this->getPath()=='/warehouse/settings/edit_warehouse'&&isset($_GET['warehouseid']))
        {
            if(isset($_GET['delete'])&&$_GET['delete']=="true")
            {
                if(Warehouse::deleteWarehouseByID(htmlspecialchars($_GET['warehouseid'])))
                {
                    echo '<script>alert("Warehouse successfully deleted"); window.location = "/warehouse/settings";</script>';
                    exit;
                }
                else
                {
                    echo '<script>alert("Warehouse could not be deleted");</script>';
                    exit;
                }
            }
            $this->setTitle('Edit warehouse - Warehouse Manager');
            $this->fragmentArray['title'] = 'Edit warehouse';
            $warehouse = Warehouse::getWarehouseByID(htmlspecialchars($_GET['warehouseid']));
            if($warehouse)
            {
                $this->fragmentArray['warehousename'] = $warehouse->getWarehouseName();
                $this->fragmentArray['details'] = $warehouse->getDetails()?? '';
            }
        }
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditWarehouseFragment.php');
        $this->show();
    }


    public function submitWarehouse()
    {
        if(!LoginController::isAdmin())
        {
            header('Location:/warehouse/settings',true,303);
            exit;
        }
        $this->setTitle('Submitting warehouse - Warehouse Manager');
        $this->setDescription('Submit warehouse subpage of settings');
        $this->setUpMainView();
        if($this->getPath()=='/warehouse/settings/add_warehouse'&&isset($_POST['warehousename']))
        {
            $details = null;
            if(isset($_POST['details']))
            {
                $details = $_POST['details'];
            }
            if(Warehouse::addWarehouse2DB(htmlspecialchars($_POST['warehousename']),htmlspecialchars($details)))
            {
                (new Log("New warehouse: ".htmlspecialchars($_POST['warehousename']),LoginController::getUserID()))->save();
                echo '<script>alert("Warehouse successfully added"); window.location = "/warehouse/settings";</script>';
            }
        }
        else if('/warehouse/settings/edit_warehouse'&&isset($_GET['warehouseid'])&&isset($_POST['warehousename']))
        {
            $warehouse = Warehouse::getWarehouseByID(htmlspecialchars($_GET['warehouseid']));
            if(is_null($warehouse))
            {
                echo '<script>alert("Warehouse could not be loaded); window.location = "/warehouse/settings";</script>';
                exit;
            }
            $anythingSet = false;
            if($_POST['warehousename']!='')
            {
                $warehouse->setWarehouseName(htmlspecialchars($_POST['warehousename']));
                $anythingSet = true;
            }
            if(isset($_POST['details'])&&$_POST['details']!='')
            {
                $warehouse->setDetails(htmlspecialchars($_POST['details']));
                $anythingSet = true;
            }
            if($anythingSet&&$warehouse->updateWarehouseInDB())
            {
                echo '<script>alert("Warehouse successfully updated"); window.location = "/warehouse/settings";</script>';
                (new Log($warehouse->getWarehouseName()." - warehouse updated",LoginController::getUserID()))->save();
                exit;
            }
            else
            {
                echo '<script>alert("Warehouse could not be updated"); window.location = "/warehouse/settings/edit_warehouse?warehouseid='.$_GET['warehouseid'].'";</script>';
                exit;
            }
        }
        $this->show();
    }

    public function editUser()
    {
        if(!LoginController::isAdmin())
        {
            header('Location:/warehouse/settings',true,303);
            exit;
        }
        $this->setTitle('Edit user - Warehouse Manager');
        $this->setDescription('Edit user subpage of settings');
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditUserFragment.php');
        $this->fragmentArray['title'] = "Edit user";
        $user = User::getUserByID($_GET['userid']);
        //User not in database
        if(is_null($user))
        {
            echo '<script>alert("Warehouse could not be loaded); window.location = "/warehouse/settings";</script>';
            exit;
        }
        $this->fragmentArray['username'] = $user->getUserName();
        $this->fragmentArray['priviligelevel'] = $user->getPriviligeLevel();
        //edit user page deleting user
        if($this->getPath()=='/warehouse/settings/edit_user'&&isset($_GET['userid'])&&isset($_GET['delete'])&&$_GET['delete']=='true')
        {
            if(User::deleteUserByID(htmlspecialchars($_GET['userid'])))
            {
                (new Log("Deleted user: ".$user->getUserName(),LoginController::getUserID()))->save();
                echo '<script>alert("User successfully deleted"); window.location = "/warehouse/settings";</script>';
                exit;
            }
            else
            {
                echo '<script>alert("User could not be deleted"); window.location = "/warehouse/settings/edit_user?userid='.$_GET['userid'].' ";</script>';
                exit;
            }
        }
        $this->show();
    }

    public function editCurrentUser()
    {
        //editing currently logged in user's own account
        if($this->getPath()=='/warehouse/settings'&&LoginController::getUserID())
        {
            $user = User::getUserByID(LoginController::getUserID());
            //check if loading is successful
            if(!$user)
            {
                echo '<script>alert("User could not be loaded"); window.location = "/warehouse/settings";</script>';
                exit;
            }
            $anythingSet = false;
            if(isset($_POST['password'])&&$_POST['password']!=""&&$this->checkPass($_POST['password']))
            {              
                $user->setPassword(htmlspecialchars($_POST['password']));
                $anythingSet = true;
            }
            if(isset($_POST['username'])&&$_POST['username']!=""&&$this->checkUsername($_POST['username']))
            {
                $user->setUserName(htmlspecialchars($_POST['username']));
                $anythingSet = true;
            }
            if(isset($_POST['privilegelevel'])&&$_POST['privilegelevel']!="")
            {
                $user->setPriviligeLevel($_POST['privilegelevel']);
                $anythingSet = true;
            }
            if($anythingSet&&$user->updateUserInDB()&&LoginController::updateSessionUser())
            {
                (new Log("User parameters updated: ".$user->getUserName(),LoginController::getUserID()))->save();
                echo '<script>alert("User successfully updated"); window.location = "/warehouse/settings";</script>';
                exit;
            }
            else
            {
                echo '<script>alert("User could not be updated"); window.location = "/warehouse/settings";</script>';
                exit;
            }
        }
    }

    //Add new user page
    public function addUser()
    {
        if(!LoginController::isAdmin())
        {
            header('Location:/warehouse/settings',true,303);
            exit;
        }
        $this->setTitle('New user - Warehouse Manager');
        $this->setDescription('New user subpage of settings');
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditUserFragment.php');
        $this->fragmentArray['title'] = "New user";
        $this->show();
    }

    //post request handler for add and edit user
    public function submitUser()
    {
        if(!LoginController::isAdmin())
        {
            header('Location:/warehouse/settings',true,303);
            exit;
        }
        $this->setTitle('Submitting user - Warehouse Manager');
        $this->setDescription('Submit user subpage of settings');
        $this->setUpMainView();
        $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditUserFragment.php');

        //check validity write to UI
        if(isset($_POST['username'])&&$_POST['username']!="")
        {
            $this->fragmentArray['validusername'] = $this->checkUsername($_POST['username']);
        }
        if(isset($_POST['password'])&&$_POST['password']!="")
        {
            $this->fragmentArray['validpassword'] = $this->checkPass($_POST['password']);
        }
        //checking validity of password
        if($this->checkUsername($_POST['username'])&&$this->checkPass($_POST['password']))
        {
            //convert string to privilige level
            $privilegelevel = User::str2Privilige($_POST['privilegelevel']);
            //new user is being added
            if($this->getPath()=='/warehouse/settings/add_user')
            {
                if(User::registerUser(htmlspecialchars($_POST['username']),htmlspecialchars($_POST['password']),$privilegelevel))
                {
                    (new Log("New user created: ".$_POST['username'],LoginController::getUserID()))->save();
                    echo '<script>alert("User successfully saved"); window.location = "/warehouse/settings";</script>';
                    exit;
                }
                else
                {
                    echo '<script>alert("User could not be saved"); window.location = "/warehouse/settings/add_user ";</script>';
                    exit;
                }
            }
        }
        //existing user is being edited
        if($this->getPath()=='/warehouse/settings/edit_user'&&isset($_GET['userid']))
        {
            $user = User::getUserByID(htmlspecialchars($_GET['userid']));
            //check if loading is successful
            if(!$user)
            {
                echo '<script>alert("User could not be loaded"); window.location = "/warehouse/settings/edit_user?userid='.$_GET['userid'].' ";</script>';
                exit;
            }
            $anythingSet = false;
            if(isset($_POST['password'])&&$_POST['password']!=""&&$this->checkPass($_POST['password']))
            {              
                $user->setPassword(htmlspecialchars($_POST['password']));
                $anythingSet = true;
            }
            if(isset($_POST['username'])&&$_POST['username']!=""&&$this->checkUsername($_POST['username']))
            {
                $user->setUserName(htmlspecialchars($_POST['username']));
                $anythingSet = true;
            }
            if(isset($_POST['privilegelevel'])&&$_POST['privilegelevel']!="")
            {
                $user->setPriviligeLevel($_POST['privilegelevel']);
                $anythingSet = true;
            }
            if($anythingSet&&$user->updateUserInDB())
            {
                (new Log("User parameters updated: ".$user->getUserName(),LoginController::getUserID()))->save();
                echo '<script>alert("User successfully updated"); window.location = "/warehouse/settings";</script>';
                exit;
            }
            else
            {
                echo '<script>alert("User could not be updated"); window.location = "/warehouse/settings/edit_user?userid='.$_GET['userid'].' ";</script>';
                exit;
            }
        }
        $this->show();
    }
}

?>