<?php

declare(strict_types=1);

class Warehouse
{
    private int $warehouseId;
    private string $warehouseName;
    private string $details;
    private const WAREHOUSETABLE = "Warehouses";
    private const WAREHOUSEID = "WarehouseID";
    private const WAREHOUSENAME = "WarehouseName";
    private const DETAILS = "AdditionalDetails";

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

    public static function getAllWarehouses()
    {
        $db = DB::getInstance();
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
        return null;
    }
}
?>