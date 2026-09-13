<?php
// controller base com funcoes de renderizacao de views e redirecionamento

namespace App\Core;

//========================================================
//= Classe base para os controllers da aplicacao
//========================================================

abstract class Controller
{

    // Renderiza o arquivo de visualizacao passando os dados
    protected function view(string $viewPath, array $data = []): void
    {

        extract($data);

        $file = VIEW_PATH . '/' . str_replace('.', '/', $viewPath) . '.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            die("View nao encontrada: {$viewPath}");
        }
    }

    // Redireciona o fluxo para outra rota interna
    protected function redirect(string $path): void
    {
        $url = BASE_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }
}
