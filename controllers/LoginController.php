<?php

declare(strict_types=1);
class LoginController extends BaseController
{
    public const KEY_LOGINSUCCESS = 'loginSuccess';
    public const KEY_USERID = 'userID';
    private const KEY_ADMIN = 'admin';

    public function getLoginSuccess()
    {
        //Didn't try to login yet
        if(!isset($_SESSION[self::KEY_LOGINSUCCESS]))
        {
            return null;
        }
        return $_SESSION[self::KEY_LOGINSUCCESS]? 'Successful sign-in!':'Unsuccessful sign-in!';
    }

    public function index()
    {
        if(self::loggedIn())
        {
            header("Location: /warehouse",true,303);
            exit;
        }
        $this->setTitle('Login - Warehouse Manager');
        $this->setBodyPath(parent::VIEWS.'\LoginView\Login.php');
        $this->setStyleSheetPath('..\views\LoginView\Login.css');
        $this->show();
    }

    public function login()
    {
        //be careful with the user typed data -> restrict it
        $this->setTitle('Logging in - Warehouse Manager');
        $this->setStyleSheetPath('..\views\LoginView\LoginAnimation.css');
        $this->setBodyPath(parent::VIEWS.'\LoginView\LoginAnimation.php');
        $this->show();
        $user = User::loadUser(htmlspecialchars($_POST['username']),htmlspecialchars($_POST['password']));
        if(is_null($user))
        {
            $_SESSION[self::KEY_LOGINSUCCESS] = false;
            header("Refresh:2; url= /",true,303);
            exit;
        }
        else
        {
            $_SESSION[self::KEY_LOGINSUCCESS] = true;
            $_SESSION[self::KEY_USERID] = $user->getUserID();
            $_SESSION[self::KEY_ADMIN] = $user->getPriviligeLevel() === PrivilegeLevels::ADMIN;
            header("Refresh:3; url= /warehouse",true,303);
            exit;
        }
    }

    public function logout()
    {
        if(self::loggedIn())
        {
            session_destroy();
        }
        header('Location: /',true,303);
        exit;
    }

    public static function isAdmin()
    {
        return $_SESSION[self::KEY_ADMIN]?? false;
    }

    public static function getUserID()
    {
        return $_SESSION[self::KEY_USERID]?? null;
    }

    public static function loggedIn()
    {
        return isset($_SESSION[self::KEY_LOGINSUCCESS])&&isset($_SESSION[self::KEY_USERID])&&$_SESSION[LoginController::KEY_LOGINSUCCESS]==true;
    }
}