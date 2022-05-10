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
            //resolving request
            $this->router->resolve($i_route,$i_method);
        }
        catch(PageNotFoundException|FileNotFoundException $e)
        {
            if($e instanceof PageNotFoundException)
            {
                //handling 404
                http_response_code(404);
                (new _404Controller())->index();
                exit;
            }
            echo $e->getMessage();
        }
    }
}