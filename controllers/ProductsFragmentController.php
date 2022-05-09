<?php
    class ProductsFragmentController extends MainController
    {
        public function index()
        {
            $this->setTitle('Products - Warehouse Manager');
            $this->setDescription('Page for products management');
            $this->setUpMainView();
            $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\ProductsFragment.php');
            $this->fragmentArray['title'] = 'Products';
            if(isset($_GET['page']))
            {
                $this->fragmentArray['products'] = Product::getNth100Products(htmlspecialchars($_GET['page']));
                $this->fragmentArray['page_start'] = $_GET['page']*100;
                $this->fragmentArray['page_end'] = $_GET['page']*100 + 100;
            }
            else
            {
                $this->fragmentArray['products'] = Product::getNth100Products(0);
                $this->fragmentArray['page_start'] = 0;
                $this->fragmentArray['page_end'] = 100;
            }
            $this->show();
        }

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

        public function editProduct()
        {
            if(!isset($_GET['productid']))
            {
                header('Location: /warehouse/products',true,303);
                exit;
            }
            $this->setTitle('Edit product - Warehouse Manager');
            $this->setDescription('Page for editing product.');
            $this->setUpMainView();
            $this->fragmentArray['title'] = 'Edit product';
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
            $product = Product::getProductByID(htmlspecialchars($_GET['productid']));

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

        public function submitProduct()
        {
            $this->setTitle('Edit product - Warehouse Manager');
            $this->setDescription('Page for editing product.');
            $this->setUpMainView();
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
                    }
                    else
                    {
                        echo '<script>alert("Product could not be added, check if item number is already in use"); window.location = "/warehouse/products/add_product";</script>';
                    }
                }
                else
                {
                    echo '<script>alert("Product could not be created"); window.location = "/warehouse/products/add_product";</script>';
                }
                
            }
            else if($this->getPath()=='/warehouse/products/edit_product'&&isset($_GET['productid']))
            {
                $product = Product::getProductByID(htmlspecialchars($_GET['productid']));
                if(is_null($product))
                {
                    echo '<script>alert("Product could not be loaded); window.location = "/warehouse/products";</script>';
                    exit;
                }
                $anythingSet = false;
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
                }
            }
        }
    }
?>