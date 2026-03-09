<?php

/**
 * Router - Xử lý định tuyến URL
 */
class Router
{
    private $routes = [];

    /**
     * Đăng ký route GET
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * Đăng ký route POST
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Dispatch request đến controller tương ứng
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        // Tìm route khớp chính xác
        if (isset($this->routes[$method][$uri])) {
            return $this->callAction($this->routes[$method][$uri]);
        }

        // Tìm route có tham số (ví dụ: /admin/products/edit/{id})
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Bỏ full match
                return $this->callAction($callback, $matches);
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once BASE_PATH . '/views/errors/404.php';
    }

    /**
     * Gọi controller@method
     */
    private function callAction($callback, $params = [])
    {
        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);

            // Xác định đường dẫn controller
            $controllerFile = BASE_PATH . '/app/Controllers/' . str_replace('\\', '/', $controller) . '.php';

            if (!file_exists($controllerFile)) {
                throw new Exception("Controller not found: {$controller}");
            }

            require_once $controllerFile;

            // Lấy tên class (không có namespace path)
            $className = basename(str_replace('\\', '/', $controller));
            $controllerInstance = new $className();

            return call_user_func_array([$controllerInstance, $method], $params);
        }

        // Nếu callback là closure
        return call_user_func_array($callback, $params);
    }

    /**
     * Lấy URI từ request
     */
    private function getUri()
    {
        $uri = $_SERVER['REQUEST_URI'];

        // Loại bỏ query string
        $uri = strtok($uri, '?');

        // Loại bỏ base path
        $basePath = parse_url(APP_URL, PHP_URL_PATH);
        if ($basePath && $basePath !== '/') {
            $uri = substr($uri, strlen($basePath));
        }

        // Normalize
        $uri = '/' . trim($uri, '/');

        return $uri;
    }
}
