<?php

declare (strict_types=1);

class Router
{
    private array $routes;
    private function register(string $route,string $method,array $action)
    {
        $this->routes[$route][$method] = $action;
    }

    public function get(string $route, array $action)
    {
        $this->register($route,'get',$action);
    }

    public function post(string $route, array $action)
    {
        $this->register($route,'post',$action);
    }

    public function resolve(string $request,string $method)
    {
        $requestArray = explode('?',$request);
        $route = $requestArray[0];
        $action = $this->routes[$route][$method] ?? null;
        if(!$action||!is_array($action))
        {
            throw new PageNotFoundException($route.' could not be resolved.');
        }
        [$class, $fun] = $action;
        if(class_exists($class))
        {
            $object = new $class;

            if(method_exists($object,$fun))
            {
                if($object instanceof BaseController)
                {
                    $object->setPath($route);
                }
                call_user_func_array([$object,$fun],[]);
            }
        }
    }
}