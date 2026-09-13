<?php
// roteador de requisicoes responsavel por direcionar as urls para seus respectivos controllers

namespace App\Core;

//========================================================
//= Roteador de requisicoes http
//========================================================

class Router
{
    private array $routes = [];

    // Registra rota do tipo get
    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    // Registra rota do tipo post
    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    // Adiciona a rota na lista interna
    private function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method'  => $method,
            'path'    => '/' . trim($path, '/'),
            'handler' => $handler
        ];
    }

    // Processa a requisicao e chama o controller correspondente
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove a pasta base caso a aplicacao esteja em subdiretorio
        $baseFolder = parse_url(BASE_URL, PHP_URL_PATH);
        if ($baseFolder && strpos($requestUri, $baseFolder) === 0) {
            $requestUri = substr($requestUri, strlen($baseFolder));
        }

        $uri = '/' . trim($requestUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $uri) {
                [$controllerClass, $action] = $route['handler'];

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        $controller->$action();
                        return;
                    }
                }
            }
        }

        // Retorna erro 404 para rotas inexistentes
        http_response_code(404);
        echo "404 - Pagina nao encontrada.";
    }
}
