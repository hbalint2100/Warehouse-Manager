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
        $this->setPriviligeLevel($i_privilegeLevel);
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
        if(is_null($i_username)||is_null($i_password))
        {
            return null;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT * FROM '.self::USERTABLE.' WHERE '.self::USERNAME.'=:username');
            $query->bindParam(':username',$i_username);
            $query->execute();
            $user = $query->fetchAll(PDO::FETCH_ASSOC);
            if($user&&count($user)===1&&password_verify($i_password,$user[0]['Password']))
            {
                return new User($user[0][self::USERID],$user[0][self::USERNAME],$user[0][self::PASSWORD],$user[0][self::PRIVILIGELEVEL]);
            }
            else if(self::isUserTableEmpty()&&$i_username==='admin'&&$i_password==='admin')
            {
                //default user
                return self::registerUser('admin','admin',PrivilegeLevels::ADMIN);
            }
        }
        catch(PDOException $e)
        {
            return null;
        }
        return null;
    }

    public static function registerUser(string $i_username,string $i_password,?PrivilegeLevels $i_privilegeLevel)
    {
        if(is_null($i_username)||is_null($i_password)||is_null($i_privilegeLevel))
        {
            return null;
        }
        $i_hashPassword = password_hash($i_password,PASSWORD_DEFAULT);
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('INSERT INTO '.self::USERTABLE.' ('.self::USERNAME.','.self::PASSWORD.','.self::PRIVILIGELEVEL.') VALUES (:username,:password,:priviligelevel)');
            $query->bindParam(':username',$i_username);
            $query->bindParam(':password',$i_hashPassword);
            $level = $i_privilegeLevel===PrivilegeLevels::ADMIN ? 'admin' : ($i_privilegeLevel===PrivilegeLevels::USER? 'user' : null );
            $query->bindParam(':priviligelevel',$level);
            $query->execute();
        }
        catch(PDOException $e)
        {
            return null;
        }
        $id = (int) $db->lastInsertId();
        return new User($id,$i_username,$i_hashPassword,$i_privilegeLevel);
    }

    public static function getUserByID(int $i_userId)
    {
        if(is_null($i_userId))
        {
            return null;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT * FROM '.self::USERTABLE.' WHERE '.self::USERID.'=:userid');
            $query->bindParam(':userid',$i_userId);
            $query->execute();
            $user = $query->fetchAll(PDO::FETCH_ASSOC);
            if($user&&count($user)===1)
            {
                return new User($user[0][self::USERID],$user[0][self::USERNAME],$user[0][self::PASSWORD],$user[0][self::PRIVILIGELEVEL]);
            }
        }
        catch(PDOException $e)
        {
            return null;
        }
        return null;
    }

    public static function deleteUserByID(int $i_userId)
    {
        if(is_null($i_userId))
        {
            return false;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('DELETE FROM '.self::USERTABLE.' WHERE '.self::USERID.'=:userid');
            $query->bindParam(':userid',$i_userId);
            $query->execute();
        }
        catch(PDOException $e)
        {
            return false;
        }
        return true;
    }

    public static function getAllUsers()
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT * FROM '.self::USERTABLE);
            $query->execute();
            $users = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!is_null($users))
            {
                $userArray = array();
                foreach($users as $user)
                {
                    array_push($userArray,new User($user[self::USERID],$user[self::USERNAME],$user[self::PASSWORD],$user[self::PRIVILIGELEVEL]));
                }
                return $userArray;
            }
        }
        catch(PDOException $e)
        {
            return null;
        }
        return null;
    }


    public function updateUserInDB()
    {
        if(!is_null($this->userId))
        {
            $db = DB::getInstance();
            try
            {
                $query = $db->prepare('UPDATE '.self::USERTABLE.' SET '.self::USERNAME.'=:username, '.self::PASSWORD.'=:password, '.self::PRIVILIGELEVEL.'=:priviligelevel WHERE '.self::USERID.'=:userid');
                $query->bindParam(':username',$this->username);
                $query->bindParam(':password',$this->hashPassword);
                $level = $this->privilegeLevel===PrivilegeLevels::ADMIN ? 'admin' : ($this->privilegeLevel===PrivilegeLevels::USER? 'user' : null );
                $query->bindParam(':priviligelevel',$level);
                $query->bindParam('userid',$this->userId);
                $query->execute();
            }
            catch(PDOException $e)
            {
                return false;
            }
            
            return true;
        }
        return false;
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

    public function setUserName(string $i_username)
    {
        $this->username = $i_username;
    }

    //sets user's password to the hashed version of input
    public function setPassword(string $i_password)
    {
        $this->hashPassword = password_hash($i_password,PASSWORD_DEFAULT);
    }

    public function setPriviligeLevel(PrivilegeLevels|string $i_privilegeLevel)
    {
        if(is_string($i_privilegeLevel))
        {
            $this->privilegeLevel = self::str2Privilige($i_privilegeLevel);
            return;
        }
        $this->privilegeLevel = $i_privilegeLevel;
    }


    public static function privilige2Str(PrivilegeLevels $i_privilegeLevel)
    {
        if($i_privilegeLevel===PrivilegeLevels::ADMIN)
        {
            return 'admin';
        }   
        else if($i_privilegeLevel===PrivilegeLevels::USER)
        {
            return 'user';
        }
        return null;
    }

    public static function str2Privilige(string $i_privilegeLevel)
    {
        if(is_string($i_privilegeLevel))
        {
            if(strtolower($i_privilegeLevel)==='admin')
            {
                return PrivilegeLevels::ADMIN;
            }
            else if(strtolower($i_privilegeLevel)==='user')
            {
                return PrivilegeLevels::USER;
            }
            else
            {
                throw new Exception('Illegal privilege level!');
            }
        }
        return null;
    }
}