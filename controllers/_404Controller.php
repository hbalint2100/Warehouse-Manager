<?php
//404 view controller for -> page not found exceptions
declare(strict_types=1);
class _404Controller extends BaseController
{
    public function index()
    {
        $this->setTitle('404 - Page not found');
        $this->setBodyPath(parent::VIEWS.'\404View\404.html');
        $this->setStyleSheetPath('/../views/404View/404.css');
        $this->show();
    }
}