<?php

namespace App\Router;

class Router {
    
    /**
     * @var string
     */
    private $url;    
    /**
     * @var array Array of all routes 
     */
    private $routes = [];
    /**
     * @var array
     */
    private $namesRoutes = [];



    public function __construct($url)
    {
        $this->url = $url;
    }
        
    /**
     * Detect if url is a GET method
     *
     * @param  string $path
     * @param  mixed $target
     * @param  string|null $name
     */
    public function get($path, $target, $name = null)
    {
        return $this->map($path, $target, $name, 'GET');
    }
    
    /**
     * Detect if url is a POST method
     *
     * @param  string $path
     * @param  mixed $target
     * @param  string|null $name
     */
    public function post($path, $target, $name = null)
    {
        return $this->map($path, $target, $name, 'POST');   
    }

    /**
     * Map a route to a target
     *
     * @param  string $path
     * @param  mixed $target
     * @param  string|null $name
     * @param  string $method One of 5 HTTP Methods (GET|POST|PATCH|PUT|DELETE)
     * @return array $route
     */
    public function map($path, $target, $name, $method)
    {
        $route = new Route($path, $target);
        $this->routes[$method][]= $route;
        if($name){
            if (isset($this->namedRoutes[$name])) {
                throw new \Exception("Can not redeclare route '{$name}'");
            }
            $this->namedRoutes[$name] = $route;
        }
        return $route;
    }
    
    /**
     * Check if the method is valid & check if the url match
     *
     * @return function call()
     */
    public function run()
    {
        if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new RouterException('REQUEST_METHOD does not exist');
        }
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }
        throw new RouterException('No matching routes');
        }
}