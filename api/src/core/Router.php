<?php

namespace App\Core;

class Router
{
  private static array $routes = [];

  private static $router;


  private function __construct() {}


  public static function getRouter(): Router
  {
    if (!isset(self::$router)) {
      self::$router = new Router();
    }

    return self::$router;
  }


  private function register(string $route, string $method, array|callable $action)
  {
    // Trim slashes
    $route = trim($route, '/');

    // Assign action to the passed route
    self::$routes[$method][$route] = $action;
  }


  public function get(string $route, array|callable $action)
  {
    $this->register($route, 'GET', $action);
  }


  public function post(string $route, array|callable $action)
  {
    $this->register($route, 'POST', $action);
  }


  public function dispatch()
  {
    // Get the requested route
    $requestedRoute = $this->getURI();

    if (
      !isset(self::$routes[$_SERVER['REQUEST_METHOD']]) ||
      !$this->hasMatchingRoute($requestedRoute)
    ) {
      $allowedMethods = implode(
        ', ',
        $this->setAllowedMethods($requestedRoute)
      );
      $this->abort(
        "Can't find the route '$requestedRoute' for the method requested ({$_SERVER['REQUEST_METHOD']}). Allowed methods: $allowedMethods.",
        405
      );
    }

    $routes = self::$routes[$_SERVER['REQUEST_METHOD']];

    foreach ($routes as $route => $action) {
      $routeRegex = preg_replace(
        '/{\w+}/',
        '([a-zA-Z0-9_-]+)',
        $route
      );

      $routeRegex = '@^' . $routeRegex . '$@';

      // Check if the requested route matches the current route pattern.
      if (preg_match($routeRegex, $requestedRoute, $matches)) {
        // Get all user requested path params values after removing the first matches.
        array_shift($matches);
        $routeParamsValues = $matches;

        // Find all route params names from route and save in $routeParamsNames

        $routeParamsNames = [];

        // /{(\w+)*}/ is just a trick to get 'id' out of '{id}'
        if (preg_match_all('/{(\w+)*}/', $route, $matches)) {
          $routeParamsNames = $matches[1];
        }

        // Combine between route parameter names and user provided parameter values.
        $routeParams = array_combine($routeParamsNames, $routeParamsValues);

        return $this->resolveAction($action, $routeParams);
      }
    }
    return $this->abort();
  }

  private function resolveAction($action, $routeParams)
  {
    if (is_callable($action)) {
      return call_user_func_array($action, $routeParams);
    } else if (is_array($action)) {
      return call_user_func_array([new $action[0], $action[1]], $routeParams);
    }
  }

  private function abort(
    string $message = "No matching route found.",
    int $code = 404
  ) {
    http_response_code($code);
    echo json_encode([
      'status' => $code,
      'message' => $message,
    ]);
    exit();
  }

  private function getURI()
  {
    $requestUri = $_SERVER['REQUEST_URI'];
    $absoluteRootPath = dirname($_SERVER['SCRIPT_FILENAME']);

    // Convert the absolute path to a URI-compatible format
    $rootFolder = str_replace('\\', '/', $absoluteRootPath);
    $rootFolder = '/' . trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $rootFolder), '/');

    // Remove the root folder from the request URI
    $modifiedUri = str_replace($rootFolder, '', $requestUri);

    // Optionally, trim any leading and trailing slashes
    $modifiedUri = trim($modifiedUri, '/');

    return $modifiedUri;
  }

  private function setAllowedMethods($path)
  {
    $allowedMethods = [];
    foreach (self::$routes as $method => $routes) {
      if ($this->hasMatchingRoute($path, $method)) {
        $allowedMethods[] = $method;
      }
    }

    $header = "Allow: " . implode(', ', $allowedMethods);
    header($header);

    return $allowedMethods;
  }

  private function hasMatchingRoute($path, $method = null)
  {
    if (!isset($method)) {
      $method = $_SERVER['REQUEST_METHOD'];
    }

    $routes = self::$routes[$method];

    foreach ($routes as $route => $action) {
      $routeRegex = preg_replace(
        '/{\w+}/',
        '([a-zA-Z0-9_-]+)',
        $route
      );

      $routeRegex = '@^' . $routeRegex . '$@';

      // Check if there is a route corresponding to the current one
      if (preg_match($routeRegex, $path)) {
        return true;
      }
    }

    return false;
  }
}
