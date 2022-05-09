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

    public function __construct(int $i_productId = 0,string $i_itemNumber,string $i_name,string $i_category = null, int $i_netPrice,?int $i_grossPrice = 0,?array $i_stocks)
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
            .Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSENAME.', '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::DETAILS.' FROM '.self::PRODUCT_TABLE.' LEFT OUTER JOIN '
            .Stock::STOCKTABLE.' ON '.Stock::STOCKTABLE.'.'.Stock::PRODUCTID.' = '.self::PRODUCT_TABLE.'.'.self::PRODUCTID.' LEFT OUTER JOIN '
            .Warehouse::WAREHOUSETABLE.' ON '.Stock::STOCKTABLE.'.'.Stock::WAREHOUSEID.' = '.Warehouse::WAREHOUSETABLE.'.'.Warehouse::WAREHOUSEID.
            ' WHERE '.Product::PRODUCT_TABLE.'.'.Product::PRODUCTID.' =:productid;');

            $query->bindParam(':productid',$i_productId);
            $query->execute();
            $stocks = $query->fetchAll(PDO::FETCH_ASSOC);

            if($stocks)
            {
                $stockArray = array();
                foreach($stocks as $stock)
                {
                    if($stock[Warehouse::WAREHOUSEID]&&$stock[Warehouse::WAREHOUSENAME]&&$stock[Stock::AMOUNT])
                    {
                        array_push($stockArray,new Stock(new Warehouse($stock[Warehouse::WAREHOUSEID],$stock[Warehouse::WAREHOUSENAME],$stock[Warehouse::DETAILS]),$stock[Stock::AMOUNT]));
                    }
                }
                return new Product($stocks[0][self::PRODUCTID],$stocks[0][self::ITEMNUMBER],$stocks[0][self::NAME],$stocks[0][self::CATEGORY],(int) $stocks[0][self::NETPRICE],(int) $stocks[0][self::GROSSPRICE],$stockArray);
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

    public function insertProduct2DB()
    {
        if(!is_null($this->name))
        {
            $db = DB::getInstance();
            try
            {
                $db->beginTransaction();
                $query = $db->prepare('INSERT INTO '.self::PRODUCT_TABLE.' ( '.self::NAME.', '.self::ITEMNUMBER.', '.self::CATEGORY.', '.self::NETPRICE.', '.self::GROSSPRICE.
                ') VALUES(:name,:itemnumber,:category,:netprice,:grossprice)');
                $query->bindParam(':name',$this->name);
                $query->bindParam(':itemnumber',$this->itemNumber);
                $query->bindParam(':category',$this->category);
                $query->bindParam(':netprice',$this->netPrice);
                $query->bindParam(':grossprice',$this->grossPrice);
                $query->execute();
                $this->productId = (int) $db->lastInsertId();
                foreach($this->stocks as $stock)
                {
                    if(is_null($stock->insertStock2DB($this->productId)))
                    {
                        throw new Exception('Insert fail');
                    }
                }
                $db->commit();
                return $this;
            }
            catch(Exception|PDOException $e)
            {
                $db->rollBack();
                return null;
            }
        }
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
                    $query3 = $db->prepare('SELECT COUNT(*) AS EXISTING FROM '.Stock::STOCKTABLE.' WHERE '.Stock::WAREHOUSEID.' = :warehouseid AND '.Stock::PRODUCTID.' = :productid;' );
                    $warehouseId = $stock->getWarehouse()->getWarehouseId();
                    $query3->bindParam(':warehouseid',$warehouseId);
                    $query3->bindParam(':productid',$this->productId);
                    $query3->execute();
                    $exists = $query3->fetchAll(PDO::FETCH_ASSOC)[0]['EXISTING'];
                    if($exists)
                    {
                        $query2 = $db->prepare('UPDATE '.Stock::STOCKTABLE.
                        ' SET '.Stock::AMOUNT.' = :amount WHERE '.Stock::STOCKTABLE.'.'.Stock::WAREHOUSEID.' = :warehouseid AND '.Stock::PRODUCTID.' = :productid;');
                        $amount = $stock->getAmount();
                        $query2->bindParam(':amount',$amount);
                        $query2->bindParam(':warehouseid',$warehouseId);
                        $query2->bindParam(':productid',$this->productId);
                        $query2->execute();
                    }
                    else
                    {
                        if(is_null($stock->insertStock2DB($this->productId)))
                        {
                            throw new Exception('Insert fail');
                        }
                    }
                }
                $db->commit();
            }
            catch(Exception|PDOException $e)
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

    public function setProductId(int $i_productId)
    {
        $this->productId = $i_productId;
    }

    public function setItemNumber(string $i_itemNumber)
    {
        $this->itemNumber = $i_itemNumber;
    }

    public function setName(string $i_name)
    {
        $this->name = $i_name;
    }

    public function setCategory(string $i_category)
    {
        $this->category = $i_category;
    }

    public function setNetPrice(int $i_netPrice)
    {
        $this->netPrice = $i_netPrice;
    }

    public function setGrossPrice(int $i_grossPrice)
    {
        $this->grossPrice = $i_grossPrice;
    }

    public function setStocks(array $i_stocks)
    {
        $this->stocks = $i_stocks;
    }

    public static function deleteProductByID(int $i_productId)
    {
        if(is_null($i_productId))
        {
            return false;
        }
        $db = DB::getInstance();
        try
        {
            $query = $db->prepare('DELETE FROM '.self::PRODUCT_TABLE.' WHERE '.self::PRODUCTID.'=:productid');
            $query->bindParam(':productid',$i_productId);
            $query->execute();
        }
        catch(PDOException $e)
        {
            return false;
        }
        return true;
    }
}


?>