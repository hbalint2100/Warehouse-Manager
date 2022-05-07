<?php 

declare(strict_types=1);
class Product
{
    private int $productId;
    private string $itemNumber;
    private string $name;
    private ?string $category;
    private int $netPrice;
    private ?int $grossPrice;
    private ?array $stocks;
    
    private const PRODUCT_TABLE = "Products";
    private const PRODUCTID = "ProductID";
    private const ITEMNUMBER = "ItemNumber";
    private const CATEGORY = "Category";
    private const NAME = "ProductName";
    private const NETPRICE = "NetPrice";
    private const GROSSPRICE = "GrossPrice";

    public function __construct(int $i_productId,string $i_itemNumber,string $i_name,string $i_category = null, int $i_netPrice,?int $i_grossPrice = 0,?array $i_stocks)
    {
        $this->productId = $i_productId;
        $this->itemNumber = $i_itemNumber;
        $this->name = $i_name;
        $this->category = $i_category;
        $this->netPrice = $i_netPrice;
        $this->grossPrice = $i_grossPrice?? 0;
        $this->stocks = $i_stocks;
    }

    public static function getProductByID($i_productId)
    {
        if(is_null($i_productId))
        {
            return null;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT '.self::PRODUCT_TABLE.'.'.self::PRODUCTID.', '.self::PRODUCT_TABLE.'.'.self::ITEMNUMBER.', '
            .self::PRODUCT_TABLE.'.'.self::CATEGORY.', '.self::PRODUCT_TABLE.'.'.self::NAME.', '.self::PRODUCT_TABLE.'.'.self::NETPRICE.', '
            .self::PRODUCT_TABLE.'.'.self::GROSSPRICE.', '.Stock::STOCKTABLE.'.'.Stock::AMOUNT.', '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSEID.', '
            .Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSENAME.', '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::DETAILS.' FROM '.self::PRODUCT_TABLE.' INNER JOIN '
            .Stock::STOCKTABLE.' ON '.Stock::STOCKTABLE.'.'.Stock::PRODUCTID.' = '.self::PRODUCT_TABLE.'.'.self::PRODUCTID.' INNER JOIN '
            .Warehouse::WAREHOUSETABLE.' ON '.Stock::STOCKTABLE.'.'.Stock::WAREHOUSEID.' = '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSEID.
            ' WHERE '.self::PRODUCT_TABLE.'.'.self::PRODUCTID.' =:productid GROUP BY '.self::PRODUCT_TABLE.'.'.self::PRODUCTID.';');

            $query->bindParam(':productid',$i_productId);
            $query->execute();
            $stocks = $query->fetchAll(PDO::FETCH_ASSOC);
            if($stocks)
            {
                $stockArray = array();
                foreach($stocks as $stock)
                {
                    array_push($stockArray,new Stock(new Warehouse($stocks[Warehouse::WAREHOUSEID],$stocks[Warehouse::WAREHOUSENAME],$stocks[Warehouse::DETAILS]),$stocks[Stock::AMOUNT]));
                }
                return new Product($stock[0][self::PRODUCTID],$stock[0][self::ITEMNUMBER],$stock[0][self::NAME],$stock[0][self::CATEGORY],$stock[0][self::NETPRICE],$stock[0][self::GROSSPRICE],$stockArray);
            }
        }
        catch(PDOException $e)
        {
            return null;
        }
        return null;

    }

    public static function getNth100Products(int $N)
    {
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('SELECT * FROM '.self::PRODUCT_TABLE.' ORDER BY '.self::ITEMNUMBER.' ASC LIMIT 100 OFFSET :offset;');
            $N*=100;
            $query->bindParam(':offset',$N,PDO::PARAM_INT);
            $query->execute();
            $products = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!is_null($products))
            {
                $productsArray = array();
                foreach($products as $product)
                {
                    array_push($productsArray,new Product($product[self::PRODUCTID],$product[self::ITEMNUMBER],$product[self::NAME],$product[self::CATEGORY],(int) $product[self::NETPRICE],(int) $product[self::GROSSPRICE],Stock::getStocksByProductID($product[self::PRODUCTID])));
                }
                return $productsArray;
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return null;
        }
        return null;
    }

    public function updateProductInDB()
    {
        if(!is_null($this->productId))
        {
            $db = DB::getInstance();
            try
            {
                $db->beginTransaction();
                $query = $db->prepare('UPDATE '.self::PRODUCT_TABLE.' SET '.self::ITEMNUMBER.'=:itemnumber, '.self::NAME.'=:name, '
                .self::CATEGORY.'=:category, '.self::NETPRICE.'=:netprice, '.self::GROSSPRICE.'=:grossprice WHERE '.self::PRODUCTID.'=:productid');
                $query->bindParam(':itemnumber',$this->itemNumber);
                $query->bindParam(':name',$this->name);
                $query->bindParam(':category',$this->category);
                $query->bindParam(':netprice',$this->netPrice);
                $query->bindParam(':grossprice',$this->grossPrice);
                $query->bindParam(':productid',$this->productId);
                $query->execute();

                foreach($this->stocks as $stock)
                {
                    $query2 = $db->prepare('UPDATE '.Stock::STOCKTABLE.' INNER JOIN '.Warehouse::WAREHOUSETABLE.' ON '
                    .Stock::STOCKTABLE.'.'.Stock::WAREHOUSEID.' = '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSEID.
                    ' SET '.Stock::STOCKTABLE.'.'.Stock::AMOUNT.' = :amount WHERE '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSENAME.' = :warehousename;');
                    $query2->bindParam(':amount',$stock->getAmount());
                    $query2->bindParam(':warehousename',$stock->getWarehouse()->getWarehouseName());
                    $query2->execute();
                }
                $db->commit();
            }
            catch(PDOException $e)
            {
                $db->rollBack();
                return false;
            }
            return true;
        }
        return false;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getItemNumber()
    {
        return $this->itemNumber;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getNetPrice()
    {
        return $this->netPrice;
    }

    public function getGrossPrice()
    {
        return $this->grossPrice;
    }

    public function getStocks()
    {
        return $this->stocks;
    }
}


?>