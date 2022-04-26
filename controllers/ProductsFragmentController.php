<?php
    class ProductsFragmentController extends MainController
    {
        public function index()
        {
            $this->setTitle('Products - Warehouse Manager');
            $this->setDescription('Page for products management');
            $this->setUpMainView();
            $this->show();
        }
    }
?>