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

    public function __construct(string $i_action,int $i_userId,int $i_logId = 0)
    {
        $this->action = $i_action;
        $this->userId = $i_userId;
        $this->logId = $i_logId;
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
}
?>