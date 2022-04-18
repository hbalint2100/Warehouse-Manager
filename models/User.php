<?php
declare(strict_types=1);

enum PrivilegeLevels
{
    case ADMIN;
    case USER;
}


class User
{
    private int $userId;
    private string $username;
    private string $hashPassword;
    private PrivilegeLevels $privilegeLevel;
    private const USERTABLE = "Users";
    private const USERID = "UserID";
    private const USERNAME = "Username";
    private const PASSWORD = "Password";
    private const PRIVILIGELEVEL = "PriviligeLevel";

    public function __construct(int $i_userId,string $i_username,string $i_hashPassword,PrivilegeLevels|string $i_privilegeLevel)
    {
        $this->userId = $i_userId;
        $this->username = $i_username;
        $this->hashPassword = $i_hashPassword;
        if(is_string($i_privilegeLevel))
        {
            if(strtolower($i_privilegeLevel)==='admin')
            {
                $this->privilegeLevel = PrivilegeLevels::ADMIN;
            }
            else if(strtolower($i_privilegeLevel)==='user')
            {
                $this->privilegeLevel = PrivilegeLevels::USER;
            }
            else
            {
                throw new Exception('Illegal privilege level!');
            }
        }
        else
        {
            $this->privilegeLevel = $i_privilegeLevel;
        }
    }

    private static function isUserTableEmpty()
    {
        $db = DB::getInstance();
        $query = $db->prepare('SELECT COUNT(*) FROM '.self::USERTABLE);
        $query->execute();
        return !($query->fetchAll(PDO::FETCH_NUM)[0][0]);
    }

    public static function loadUser(string $i_username,string $i_password)
    {
        $db = DB::getInstance();
        $query = $db->prepare('SELECT * FROM '.self::USERTABLE.' WHERE '.self::USERNAME.'=:username');
        $query->bindParam(':username',$i_username);
        $query->execute();
        $user = $query->fetchAll(PDO::FETCH_ASSOC);
        if($user&&count($user)===1&&password_verify($i_password,$user[0]['Password']))
        {
            return new User($user[0][self::USERID],$user[0][self::USERNAME],$user[0][self::PASSWORD],$user[0][self::PRIVILIGELEVEL]);
        }
        else if(self::isUserTableEmpty())
        {
            return self::registerUser('admin','admin',PrivilegeLevels::ADMIN);
        }
        return null;
    }

    public static function registerUser(string $i_username,string $i_password,PrivilegeLevels $i_privilegeLevel)
    {
        $i_hashPassword = password_hash($i_password,PASSWORD_DEFAULT);
        $db = DB::getInstance();
        $query = $db->prepare('INSERT INTO '.self::USERTABLE.' ('.self::USERNAME.','.self::PASSWORD.','.self::PRIVILIGELEVEL.') VALUES (:username,:password,:priviligelevel)');
        $query->bindParam(':username',$i_username);
        $query->bindParam(':password',$i_hashPassword);
        $level = $i_privilegeLevel===PrivilegeLevels::ADMIN ? 'admin' : ($i_privilegeLevel===PrivilegeLevels::USER? 'user' : null );
        $query->bindParam(':priviligelevel',$level);
        $query->execute();
        $id = (int) $db->lastInsertId();
        return new User($id,$i_username,$i_hashPassword,$i_privilegeLevel);
    }

    public function getUserName()
    {
        return $this->username;
    }
    public function getHashPassword()
    {
        return $this->hashPassword;
    }
    public function getPriviligeLevel()
    {
        return $this->privilegeLevel;
    }
    public function getUserID()
    {
        return $this->userId;
    }
}