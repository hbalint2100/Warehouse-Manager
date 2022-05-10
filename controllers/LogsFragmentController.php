<?php
    //Controller for logs page in mainview
    class LogsFragmentController extends MainController
    {
        //displays logs
        public function index()
        {
            $this->checkLogin();
            //only available for admin users   
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
            //deleting old logs (60 day old)
            if(isset($_GET['deletelogs']))
            {
                $rows = Log::deleteOldLogs();
                if($rows)
                {
                    echo '<script>alert("'.$rows.' logs successfully deleted"); window.location = "/warehouse/logs";</script>';
                }
                else
                {
                    echo '<script>alert("No log was deleted"); window.location = "/warehouse/logs";</script>';
                }
            }
            //handling paging
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
            //number of pages
            $this->fragmentArray['maxSize'] = (floor(Log::getSize()/100));
            $this->show();
        }
    }
?>