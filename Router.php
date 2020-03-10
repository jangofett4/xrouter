<?php

include_once "Route.php";

class Router
{
    /** @var Route[] $routes_any */
    public static $routes_any = [];
    /** @var Route[] $routes_get */
    public static $routes_get = [];
    /** @var Route[] $routes_post */
    public static $routes_post = [];

    public static function get(string $url, $callback, ...$regexp)
    {
        array_push(Router::$routes_get, new Route($url, $callback, ...$regexp));
    }

    public static function post(string $url, $callback, ...$regexp)
    {
        array_push(Router::$routes_post, new Route($url, $callback, ...$regexp));
    }

    public static function any(string $url, $callback, ...$regexp)
    {
        array_push(Router::$routes_any, new Route($url, $callback, ...$regexp));
    }

    public static function start()
    {
        $requested = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        echo Router::do($requested, $method);
    }

    public static function do(string $url, string $method)
    {
        switch ($method) {
            case 'GET': {
                    foreach (Router::$routes_get as $route) {
                        $match = $route->match($url);
                        if ($match !== false) {
                            $callback = $route->callback;
                            return $callback(...$match);
                        }
                    }
                    foreach (Router::$routes_any as $route) {
                        $match = $route->match($url);
                        if ($match !== false) {
                            $callback = $route->callback;
                            return $callback(...$match);
                        }
                    }
                    break;
                }
            case 'POST': {
                    foreach (Router::$routes_post as $route) {
                        $match = $route->match($url);
                        if ($match !== false) {
                            $callback = $route->callback;
                            return $callback(...$match);
                        }
                    }
                    foreach (Router::$routes_any as $route) {
                        $match = $route->match($url);
                        if ($match !== false) {
                            $callback = $route->callback;
                            return $callback(...$match);
                        }
                    }
                    break;
                }
            default: {
                    foreach (Router::$routes_any as $route) {
                        $match = $route->match($url);
                        if ($match !== false) {
                            $callback = $route->callback;
                            return $callback(...$match);
                        }
                    }
                    break;
                }
        }

        http_response_code(404);
        return 'Not found';
    }

    /** Handy shortcut to header("Location: ...") */
    public static function fastswitch(string $url)
    {
        header("Location: $url");
        return ''; // might be useful for routes
    }
}
