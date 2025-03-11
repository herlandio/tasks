<?php

declare(strict_types=1);

namespace Routes;

use \Exception;

/**
 * Class Router
 * 
 * Responsible for managing application routes and directing requests to the appropriate handlers.
 */
class Router
{
    /** @var array $routes Stores registered routes, separated by HTTP method. */
    private array $routes = [];

    /**
     * Router constructor.
     * 
     * Initializes the router with empty arrays for GET, POST, PUT, and DELETE routes.
     */
    public function __construct()
    {
        $this->routes = [
            'GET' => [],
            'POST' => [],
            'PUT' => [],
            'DELETE' => []
        ];
    }

    /**
     * Registers a GET route.
     *
     * @param string $uri The route URI.
     * @param array $handler The handler to be called when the route is accessed.
     *                       The handler should be an array containing the class and method to be called.
     */
    public function get(string $uri, array $handler): void
    {
        $this->routes['GET'][$uri] = $handler;
    }

    /**
     * Registers a POST route.
     *
     * @param string $uri The route URI.
     * @param array $handler The handler to be called when the route is accessed.
     *                       The handler should be an array containing the class and method to be called.
     */
    public function post(string $uri, array $handler): void
    {
        $this->routes['POST'][$uri] = $handler;
    }

    /**
     * Registers a PUT route.
     *
     * @param string $uri The route URI.
     * @param array $handler The handler to be called when the route is accessed.
     *                       The handler should be an array containing the class and method to be called.
     */
    public function put(string $uri, array $handler): void
    {
        $this->routes['PUT'][$uri] = $handler;
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $uri The route URI.
     * @param array $handler The handler to be called when the route is accessed.
     *                       The handler should be an array containing the class and method to be called.
     */
    public function delete(string $uri, array $handler): void
    {
        $this->routes['DELETE'][$uri] = $handler;
    }

    /**
     * Handles the current request by checking if the route exists and calling the corresponding handler.
     * 
     * If the route is not found, a 404 response is returned.
     */
    public function handleRequest(): void
    {
        try {
            $uri = $_SERVER['REQUEST_URI'];
            $method = $_SERVER['REQUEST_METHOD'];

            $uri = strtok($uri, '?');

            $handler = $this->findRoute($method, $uri);

            if ($handler) {
                $this->callHandler($handler['handler'], $handler['params']);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Route not found!"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Internal Server Error: " . $e->getMessage()]);
        }
    }

    /**
     * Finds the route that matches the given method and URI.
     *
     * @param string $method The HTTP method.
     * @param string $uri The request URI.
     * @return array|null Returns an array containing the handler and parameters if a route is found, otherwise null.
     */
    private function findRoute(string $method, string $uri): ?array
    {
        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = $this->convertUriToPattern($route);
            if (preg_match($pattern, $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }
                return ['handler' => $handler, 'params' => $params];
            }
        }
        return null;
    }

    /**
     * Converts a URI with parameters into a regular expression pattern.
     *
     * @param string $uri The URI to convert.
     * @return string The regular expression pattern.
     */
    private function convertUriToPattern(string $uri): string
    {
        $pattern = preg_replace('/\(\\\d\+\)/', '(?P<id>\d+)', $uri);
        $pattern = preg_replace('/\//', '\\/', $pattern);
        return '/^' . $pattern . '$/';
    }

    /**
     * Calls the handler corresponding to the route.
     *
     * @param array $handler The handler to be called.
     * @param array $params The parameters to pass to the handler.
     * @throws Exception If the class or method does not exist.
     */
    private function callHandler(array $handler, array $params = []): void
    {
        try {
            [$class, $method] = $handler;

            if (!class_exists($class)) {
                throw new Exception("Class $class does not exist.");
            }

            if (!method_exists($class, $method)) {
                throw new Exception("Method $method does not exist in class $class.");
            }

            $instance = new $class();

            if (isset($params['id'])) {
                $params['id'] = (int) $params['id'];
            }

            call_user_func_array([$instance, $method], array_values($params));
        } catch (Exception $e) {
            throw new Exception("Error calling handler: " . $e->getMessage());
        }
    }
}