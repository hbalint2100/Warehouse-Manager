<?php
    //handles product page
    class ProductsFragmentController extends MainController
    {
        //handling get request with or without search 
        public function index()
        {
            $this->setTitle('Products - Warehouse Manager');
            $this->setDescription('Page for products management');
            $this->setUpMainView();
            $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\ProductsFragment.php');
            $this->fragmentArray['title'] = 'Products';
            //handles paging
            if(isset($_GET['page']))
            {
                $this->fragmentArray['products'] = isset($_GET['search'])&&$_GET['search']!=''? Product::getNth100ProductsWithSearch(htmlspecialchars($_GET['page']),htmlspecialchars($_GET['search'])) : Product::getNth100Products(htmlspecialchars($_GET['page']));
                $this->fragmentArray['page_start'] = $_GET['page']*100;
                $this->fragmentArray['page_end'] = $_GET['page']*100 + 100;
            }
            else
            {
                $this->fragmentArray['products'] = isset($_GET['search'])&&$_GET['search']!=''? Product::getNth100ProductsWithSearch(0,htmlspecialchars($_GET['search'])) : Product::getNth100Products(0);
                $this->fragmentArray['page_start'] = 0;
                $this->fragmentArray['page_end'] = 100;
            }
            //numb of pages
            $this->fragmentArray['maxSize'] = (floor(Product::getSize()/100));
            $this->show();
        }

        //handles add product page
        public function addProduct()
        {
            $this->setTitle('New product - Warehouse Manager');
            $this->setDescription('New product page.');
            $this->setUpMainView();
            $this->fragmentArray['title'] = 'New product';
            $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditProductFragment.php');
            $this->fragmentArray['warehouses'] = Warehouse::getAllWarehouses();
            $this->fragmentArray['warehousecount'] = count($this->fragmentArray['warehouses']);
            $this->show();
        }

        //handles edit product page
        public function editProduct()
        {
            //avoiding page requests without id
            if(!isset($_GET['productid']))
            {
                header('Location: /warehouse/products',true,303);
                exit;
            }
            $this->setTitle('Edit product - Warehouse Manager');
            $this->setDescription('Page for editing product.');
            $this->setUpMainView();
            $this->fragmentArray['title'] = 'Edit product';
            //deleting current product
            if(isset($_GET['delete'])&&$_GET['delete']=='true')
            {
                $log = new Log("Deleted product: ".Product::getProductByID(htmlspecialchars($_GET['productid']))->getName(),LoginController::getUserID());
                if(Product::deleteProductByID(htmlspecialchars($_GET['productid'])))
                {
                    echo '<script>alert("Product successfully deleted"); window.location = "/warehouse/products";</script>';
                    $log->save();
                    exit;
                }
                else
                {
                    echo '<script>alert("Product could not be deleted");</script>';
                    exit;
                }
            }
            //deleting stocks for current product
            if(isset($_GET['deletewarehouse']))
            {
                if(Stock::deleteStock(htmlspecialchars($_GET['productid']),htmlspecialchars($_GET['deletewarehouse'])))
                {
                    echo '<script>alert("Stock successfully deleted"); window.location="/warehouse/products/edit_product?productid='.$_GET['productid'].'"</script>';
                    exit;
                }
                else
                {
                    echo '<script>alert("Stock could not be deleted");</script>';
                    exit;
                }
            }
            $product = Product::getProductByID(htmlspecialchars($_GET['productid']));

            //displaying current product details
            if($product)
            {
                $this->fragmentArray['productname'] = $product->getName();
                $this->fragmentArray['itemnumber'] = $product->getItemNumber();
                $this->fragmentArray['category'] = $product->getCategory();
                $this->fragmentArray['netprice'] = $product->getNetPrice();
                $this->fragmentArray['grossprice'] = $product->getGrossPrice();
                $this->fragmentArray['stocks'] = $product->getStocks();
            }
            $this->fragmentArray['warehouses'] = Warehouse::getAllWarehouses();
            $this->fragmentArray['warehousecount'] = count($this->fragmentArray['warehouses']);
            $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditProductFragment.php');
            $this->show();
        }

        //handling post requests from add and edit product pages
        public function submitProduct()
        {
            $this->setTitle('Edit product - Warehouse Manager');
            $this->setDescription('Page for editing product.');
            $this->setUpMainView();
            //add product page
            if($this->getPath()=='/warehouse/products/add_product')
            {
                if(isset($_POST['productname'])&&isset($_POST['itemnumber'])&&isset($_POST['netprice'])&&$_POST['productname']&&$_POST['itemnumber']&&$_POST['netprice'])
                {
                    $category = isset($_POST['category'])? htmlspecialchars($_POST['category']) : null;
                    $grossprice = isset($_POST['grossprice'])? htmlspecialchars($_POST['grossprice']) : null;
                    $stocks = array();
                    //creating stocks
                    for($i = 1; $i <= $_POST['stocks_length'];$i++)
                    {   
                        if(isset($_POST['warehouseid-'.$i])&&isset($_POST['amount-'.$i])&&((int) $_POST['amount-'.$i])>0)
                        {
                            array_push($stocks,new Stock(Warehouse::getWarehouseByID(htmlspecialchars($_POST['warehouseid-'.$i])),(int) htmlspecialchars($_POST['amount-'.$i])));
                        }
                    }
                    $product = new Product(0,htmlspecialchars($_POST['itemnumber']),htmlspecialchars($_POST['productname']),$category,(int) htmlspecialchars($_POST['netprice']),$grossprice,$stocks);
                    //saving to DB
                    if($product->insertProduct2DB())
                    {
                        (new Log("New product: ".htmlspecialchars($_POST['productname']),LoginController::getUserID()))->save();
                        echo '<script>alert("Product successfully added"); window.location = "/warehouse/products";</script>';
                        exit;
                    }
                    else
                    {
                        echo '<script>alert("Product could not be added, check if item number is already in use"); window.location = "/warehouse/products/add_product";</script>';
                        exit;
                    }
                }
                else
                {
                    echo '<script>alert("Product could not be created"); window.location = "/warehouse/products/add_product";</script>';
                    exit;
                }
                
            }
            //edit product page
            else if($this->getPath()=='/warehouse/products/edit_product'&&isset($_GET['productid']))
            {
                $product = Product::getProductByID(htmlspecialchars($_GET['productid']));
                if(is_null($product))
                {
                    echo '<script>alert("Product could not be loaded); window.location = "/warehouse/products";</script>';
                    exit;
                }
                $anythingSet = false;
                //handling set parameters and updating or inserting them in db
                if(isset($_POST['itemnumber'])&&$_POST['itemnumber']!='')
                {
                    $product->setItemNumber(htmlspecialchars($_POST['itemnumber']));
                    $anythingSet = true;
                }
                if(isset($_POST['productname'])&&$_POST['productname']!='')
                {
                    $product->setName(htmlspecialchars($_POST['productname']));
                    $anythingSet = true;
                }
                if(isset($_POST['category'])&&$_POST['category']!='')
                {
                    $product->setCategory(htmlspecialchars($_POST['category']));
                    $anythingSet = true;
                }
                if(isset($_POST['netprice'])&&$_POST['netprice']!='')
                {
                    $product->setNetPrice((int) htmlspecialchars($_POST['netprice']));
                    $anythingSet = true;
                }
                if(isset($_POST['grossprice'])&&$_POST['grossprice']!='')
                {
                    $product->setGrossPrice((int) htmlspecialchars($_POST['grossprice']));
                    $anythingSet = true;
                }
                //handling warehouses and its stocks for current product
                if(isset($_POST['stocks_length']))
                {
                    $stocks = array();
                    //creating stocks
                    for($i = 1; $i <= $_POST['stocks_length'];$i++)
                    {   
                        if(isset($_POST['warehouseid-'.$i])&&isset($_POST['amount-'.$i])&&$_POST['amount-'.$i]!=''&&((int) $_POST['amount-'.$i])>=0)
                        {
                            array_push($stocks,new Stock(Warehouse::getWarehouseByID(htmlspecialchars($_POST['warehouseid-'.$i])),(int) htmlspecialchars($_POST['amount-'.$i])));
                            $anythingSet = true;
                        }
                    }
                    $product->setStocks($stocks);
                }
                if($anythingSet)
                {
                    //updating product in DB
                    if($product->updateProductInDB())
                    {
                        echo '<script>alert("Product successfully updated"); window.location = "/warehouse/products";</script>';
                        (new Log($product->getName()." - product updated",LoginController::getUserID()))->save();
                        exit;
                    }
                    else
                    {
                        echo '<script>alert("Product could not be updated, check if item number is already in use"); window.location = "/warehouse/products/edit_product?productid='.$_GET['productid'].'";</script>';
                        exit;
                    }
                }
                else
                {
                    echo '<script>alert("Product could not be updated, nothing is changed"); window.location = "/warehouse/products/edit_product?productid='.$_GET['productid'].'";</script>';
                    exit;
                }
            }
        }
    }
?>