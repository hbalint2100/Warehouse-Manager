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
            $this->setFragmentPath(parent::VIEWS.'\MainView\Fragments\LogsFragment.php');
            $this->fragmentArray['title'] = 'Logs';
            $this->fragmentArray['logs'] = Log::getNth100Logs(0);
            $this->fragmentArray['maxSize'] = (floor(Log::getSize()/100));
            $this->show();
        }
    }
?>