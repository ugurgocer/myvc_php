<?php

namespace App\Core;

class Route
{
  protected $actualPath;
  protected $actualMethod;
  protected $routes = [];
  protected $notFound;

  public function __construct($currentPath, $currentMethod)
  {
    $this->actualPath = $currentPath;
    $this->actualMethod = $currentMethod;

    $this->notFound = function(){
      http_response_code(404);
      echo '404 Not Found';
    };
  }

  public function get($path, $callback)
  {
    $this->routes[] = ['GET', $path, $callback];
  }
  
  public function post($path, $callback)
  {
    $this->routes[] = ['POST', $path, $callback];
  }

  public function run()
  {
    foreach ($this->routes as $route) {
      list($method, $path, $callback) = $route;

      $checkMethod = $this->actualMethod == $method;
      $checkPath = preg_match("~^{$path}$~ixs", $this->actualPath, $params);

      if ($checkMethod && $checkPath) {
        array_shift($params);
        return call_user_func_array($callback, $params);
      }
    }

    return call_user_func($this->notFound);
  }
}
