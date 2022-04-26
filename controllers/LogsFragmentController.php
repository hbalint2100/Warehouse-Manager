<?php
    class LogsFragmentController extends MainController
    {
        public function index()
        {
            $this->checkLogin();
            if(!LoginController::isAdmin())
            {
                header("Location:/warehouse",true,303);
                exit;
            }
            $this->setTitle('Logs - Warehouse Manager');
            $this->setDescription('Log page for warehouse database manager');
            $this->setUpMainView();
            $this->show();
        }
    }
?>