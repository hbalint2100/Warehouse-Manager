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
            $this->setTitle('Edit product - Warehouse Manager');
            $this->setDescription('Page for editing product.');
            $this->setUpMainView();
            $this->fragmentArray['title'] = 'Edit product';
            $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\EditProductFragment.php');
            $this->show();
        }
    }
?>