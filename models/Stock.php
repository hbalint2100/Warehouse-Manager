<?php 

declare(strict_types=1);
class Stock
{
    private Warehouse $warehouse;
    private int $amount;
    public const STOCKTABLE = 'Stocks';
    public const WAREHOUSEID = 'WarehouseID';
    public const PRODUCTID = 'ProductID';
    public const AMOUNT = 'Amount';

    public function __construct(Warehouse $i_warehouse,int $i_amount)
    {
        $this->warehouse = $i_warehouse;
        $this->amount = $i_amount;
    }

    public function setAmount(int $i_amount)
    {
        $this->amount = $i_amount;
    }

    public function setWarehouse($i_warehouse)
    {
        $this->warehouse = $i_warehouse;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public static function getStocksByProductID($i_productID)
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT '.self::STOCKTABLE.'.'.self::WAREHOUSEID.', '.self::STOCKTABLE.'.'.self::PRODUCTID.', '.self::STOCKTABLE.'.'
            .self::AMOUNT.', '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSENAME.', '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::DETAILS.' FROM '
            .self::STOCKTABLE.' INNER JOIN '.Warehouse::WAREHOUSETABLE.' ON '.self::STOCKTABLE.'.'.self::WAREHOUSEID.' = '.Warehouse::WAREHOUSETABLE.'.'
            .Warehouse::WAREHOUSEID.'  WHERE '.self::PRODUCTID.' = :productid;');
            $query->bindParam(':productid',$i_productID,PDO::PARAM_INT);
            $query->execute();
            $stocks = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!is_null($stocks))
            {
                $stocksArray = array();
                foreach($stocks as $stock)
                {
                    
                    array_push($stocksArray,new Stock(new Warehouse($stock[Warehouse::WAREHOUSEID],$stock[Warehouse::WAREHOUSENAME],$stock[Warehouse::DETAILS]),$stock[self::AMOUNT]));
                }
                return $stocksArray;
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