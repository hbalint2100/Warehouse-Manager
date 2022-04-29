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
            if(isset($_GET['page']))
            {
                $this->fragmentArray['logs'] = Log::getNth100Logs(htmlspecialchars($_GET['page']));
                $this->fragmentArray['page_start']=$_GET['page']*100;
                $this->fragmentArray['page_end']=$_GET['page']*100 + 100;
            }
            else
            {
                $this->fragmentArray['logs'] = Log::getNth100Logs(0);
                $this->fragmentArray['page_start'] = 0;
                $this->fragmentArray['page_end'] = 100;
            }
            $this->fragmentArray['maxSize'] = (floor(Log::getSize()/100));
            $this->show();
        }
    }
?>