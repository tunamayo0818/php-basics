<?php

class Router
{
    /** @var array<string,array>  $routes */
    private $routes = [];

    /** @var array<string,mixed>  $params */
    private $params = [];

    /* ---------- ルート登録 ---------- */

    public function get(string $route, string $controller, array $middleware = []): self
    {
        return $this->addRoute('GET', $route, $controller, $middleware);
    }

    public function post(string $route, string $controller, array $middleware = []): self
    {
        return $this->addRoute('POST', $route, $controller, $middleware);
    }

    private function addRoute(string $method, string $route, string $controller, array $middleware): self
    {
        $this->routes[$method][$this->normalizeUri($route)] = [
            'controller' => $controller,
            'middleware' => $middleware,
        ];
        return $this;
    }

    /* ---------- ディスパッチ ---------- */

    public function dispatch(): void
    {
        $url    = $this->normalizeUri(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (empty($this->routes[$method])) {
            $this->handleNotFound();
        }

        foreach ($this->routes[$method] as $route => $info) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches);
                $this->params = $matches;

                /* --- ミドルウェア --- */
                if (!empty($info['middleware']) && !$this->runMiddleware($info['middleware'])) {
                    return; // 失敗時はミドルウェア側でリダイレクト済み
                }

                /* --- コントローラ呼び出し --- */
                [$controllerName, $action] = explode('@', $info['controller']);
                $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    // グローバル名前空間のクラスを生成
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $action], $this->params);
                    return;
                }
            }
        }

        $this->handleNotFound();
    }

    /* ---------- ミドルウェア ---------- */

    private function runMiddleware(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            $fqcn = "\\Core\\" . ucfirst($middleware) . 'Middleware';
            if (class_exists($fqcn)) {
                if (!(new $fqcn())->handle()) {
                    return false;
                }
            }
        }
        return true;
    }

    /* ---------- 補助 ---------- */

    private function handleNotFound(): void
    {
        http_response_code(404);
        $errorView = __DIR__ . '/../views/errors/404.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<h1>404 Not Found</h1><p>The page you are looking for could not be found.</p>';
        }
        exit;
    }

    private function convertRouteToRegex(string $route): string
    {
        // {id} → ([^/]+) に変換
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        // 静的部分をエスケープ（/ は除外）
        $pattern = preg_replace('/([.()+?^$|#])/', '\\\\$1', $pattern);

        return '#^' . $pattern . '/?$#';
    }

    private function normalizeUri(string $uri): string
    {
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }
}
