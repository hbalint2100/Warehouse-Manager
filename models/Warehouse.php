<?php

declare(strict_types=1);

class Warehouse
{
    private int $warehouseId;
    private string $warehouseName;
    private ?string $details;
    public const WAREHOUSETABLE = "Warehouses";
    public const WAREHOUSEID = "WarehouseID";
    public const WAREHOUSENAME = "WarehouseName";
    public const DETAILS = "AdditionalDetails";

    public function __construct($i_warehouseId,$i_warehouseName,$i_details = null)
    {
        $this->warehouseId = $i_warehouseId;
        $this->warehouseName = $i_warehouseName;
        $this->details = $i_details;
    }

    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    public function setWarehouseName(string $i_warehouseName)
    {
        $this->warehouseName = $i_warehouseName;
    }

    public function setDetails(string $i_details)
    {
        $this->details = $i_details;
    }

    public static function getAllWarehouses()
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT * FROM '.self::WAREHOUSETABLE);
            $query->execute();
            $warehouses = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!is_null($warehouses))
            {
                $warehouseArray = array();
                foreach($warehouses as $warehouse)
                {
                    array_push($warehouseArray,new Warehouse($warehouse[self::WAREHOUSEID],$warehouse[self::WAREHOUSENAME],$warehouse[self::DETAILS]));
                }
                return $warehouseArray;
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
        return null;
    }

    public static function addWarehouse2DB(string $i_warehouseName, ?string $i_details = null)
    {
        if(is_null($i_warehouseName))
        {
            return null;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('INSERT INTO '.self::WAREHOUSETABLE.' ('.self::WAREHOUSENAME.','.self::DETAILS.') VALUES (:warehousename,:details)');
            $query->bindParam(':warehousename',$i_warehouseName);
            $query->bindParam(':details',$i_details);
            $query->execute();
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
        $id = (int) $db->lastInsertId();
        return new Warehouse($id,$i_warehouseName,$i_details);
    }

    public static function getWarehouseByID(int $i_warehouseId)
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT * FROM '.self::WAREHOUSETABLE.' WHERE '.self::WAREHOUSEID.'= :warehouseid');
            $query->bindParam(':warehouseid',$i_warehouseId);
            $query->execute();
            $warehouse = $query->fetchAll(PDO::FETCH_ASSOC);
            if($warehouse&&count($warehouse)===1)
            {
                return new Warehouse($warehouse[0][self::WAREHOUSEID],$warehouse[0][self::WAREHOUSENAME],$warehouse[0][self::DETAILS]);
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
        return null;
    }

    public static function deleteWarehouseByID(int $i_warehouseId)
    {
        if(is_null($i_warehouseId))
        {
            return false;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('DELETE FROM '.self::WAREHOUSETABLE.' WHERE '.self::WAREHOUSEID.'=:warehouseid');
            $query->bindParam(':warehouseid',$i_warehouseId);
            $query->execute();
        }
        catch(PDOException $e)
        {
            $e->getMessage();
            return false;
        }
        return true;
    }

    public function updateWarehouseInDB()
    {
        if(!is_null($this->warehouseId))
        {
            $db = DB::getInstance();
            try
            {
                $query = $db->prepare('UPDATE '.self::WAREHOUSETABLE.' SET '.self::WAREHOUSENAME.'=:warehousename, '.self::DETAILS.'=:details'.' WHERE '.self::WAREHOUSEID.'=:warehouseid');
                $query->bindParam(':warehousename',$this->warehouseName);
                $query->bindParam(':details',$this->details);
                $query->bindParam(':warehouseid',$this->warehouseId);
                $query->execute();
            }
            catch(PDOException $e)
            {
                echo $e->getMessage();
                return false;
            }
            
            return true;
        }
        return false;
    }
}
?>