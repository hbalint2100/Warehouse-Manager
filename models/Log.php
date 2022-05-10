<?php
class Log
{
    private const LOGTABLE = "Logs";
    private const ACTION = "Action";
    private const USERID = "UserID";
    private const TIME = "Time";
    private const LOGID = "LogID";
    
    private int $logId;
    private string $action;
    private ?string $time = null;
    private int $userId;
    private ?string $username = null;

    public function __construct(string $i_action,int $i_userId,int $i_logId = 0,string $i_username=null,string $i_time = null)
    {
        $this->action = $i_action;
        $this->userId = $i_userId;
        $this->logId = $i_logId;
        $this->username = $i_username;
        $this->time = $i_time;
    }

    public function save()
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('INSERT INTO '.self::LOGTABLE.' ('.self::ACTION.','.self::USERID.') VALUES (:action,:userid)');
            $query->bindParam(':action',$this->action);
            $query->bindParam(':userid',$this->userId);
            $query->execute();
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
        $this->logId = (int) $db->lastInsertId();
    }

    public static function deleteOldLogs()
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('DELETE FROM '.self::LOGTABLE.' WHERE TIMESTAMPDIFF(DAY,'.self::TIME.',CURRENT_TIMESTAMP()) > 60;');
            $query->execute();
            return $query->rowCount();
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return 0;
        }
    }

    public static function getNth100Logs(int $N)
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT '.self::LOGID.', '.self::ACTION.', '.self::TIME.', '.self::LOGTABLE.'.'.self::USERID.', '.User::USERNAME.' FROM '.self::LOGTABLE.' INNER JOIN '.User::USERTABLE.' ON '.self::LOGTABLE.'.'.self::USERID.' = '.User::USERTABLE.'.'.User::USERID.' ORDER BY '.self::TIME.' DESC LIMIT 100 OFFSET :offset;');
            $N*=100;
            $query->bindParam(':offset',$N,PDO::PARAM_INT);
            $query->execute();
            $logs = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!is_null($logs))
            {
                $logsArray = array();
                foreach($logs as $log)
                {
                    array_push($logsArray,new Log($log[self::ACTION],$log[self::USERID],$log[self::LOGID],$log['Username'],$log[self::TIME]));
                }
                return $logsArray;
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
        return null;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public static function getSize()
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT COUNT(*) AS Size FROM Logs');
            $query->execute();
            $logSize = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!is_null($logSize))
            {
                return $logSize[0]["Size"];
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
        return null;
    }
}
?>