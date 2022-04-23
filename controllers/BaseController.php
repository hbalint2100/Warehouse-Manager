<?php

declare(strict_types=1);
class BaseController
{
    protected const VIEWS = __DIR__.'\..\views';
    private ?string $title = null;
    private ?string $description = null;
    private ?string $bodyPath = null;
    private ?string $styleSheetPath = null;
    private ?string $favIconPath = null;
    private ?string $path = null;
    

    protected function getTitle()
    {
        return $this->title;
    }
    protected function getDescription()
    {
        return $this->description;
    }
    protected function getBodyPath()
    {
        return $this->bodyPath;
    }
    protected function getStyleSheetPath()
    {
        return $this->styleSheetPath;
    }

    protected function getFavIconPath()
    {
        return $this->styleSheetPath;
    }

    protected function setTitle(string $i_title)
    {
        $this->title = $i_title;
    }
    protected function setDescription(string $i_description)
    {
        $this->description = $i_description;
    }
    protected function setBodyPath(string $i_bodyPath)
    {
        if(!file_exists($i_bodyPath))
        {
            throw new FileNotFoundException($i_bodyPath.' is missing!');
        }
        $this->bodyPath = $i_bodyPath;
    }
    protected function setStyleSheetPath(string $i_styleSheetPath)
    {
        if(!file_exists($i_styleSheetPath)&&!file_exists(__DIR__.'\\'.$i_styleSheetPath)&&!str_starts_with($i_styleSheetPath,'http'))
        {
            throw new FileNotFoundException(__DIR__.'\\'.$i_styleSheetPath.' is missing!');
        }
        $this->styleSheetPath = $i_styleSheetPath;
    }

    protected function setFavIconPath(string $i_favIconPath)
    {
        if(!file_exists($i_favIconPath)&&!file_exists(__DIR__.'\\'.$i_favIconPath))
        {
            throw new FileNotFoundException(__DIR__.'\\'.$i_favIconPath.' is missing!');
        }
        $this->favIconPath = $i_favIconPath;
    }

    protected function show()
    {
        include_once self::VIEWS.'\BaseView.php';
    }

    public function index()
    {
        $this->show();
    }

    public function setPath(string $i_path)
    {
        $this->path = $i_path;
    }

    protected function getPath()
    {
        return $this->path;
    }
}