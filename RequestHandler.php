<?php

declare(strict_types=1);
class RequestHandler
{
    private Router $router;

    public function __construct(Router $i_router)
    {
        $this->router = $i_router;
    }

    public function handleRequest(string $i_method,string $i_route)
    {
        try
        {
            $this->router->resolve($i_route,$i_method);
        }
        catch(PageNotFoundException|FileNotFoundException $e)
        {
            echo $e->getMessage();
        }
    }
}