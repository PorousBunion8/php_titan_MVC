<?php
// autoloader nativo que carrega as classes da aplicacao sem precisar de composer
//Padrao psr4
//========================================================
//= Autoloader nativo puro
//========================================================

spl_autoload_register(function ($class) {
    // Namespace base
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    // Valida se a classe utiliza o namespace do projeto
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtem o caminho relativo da classe
    $relativeClass = substr($class, $len);

    // Mapeia o namespace para a estrutura de pastas
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Carrega o arquivo caso exista
    if (file_exists($file)) {
        require_once $file;
    }
});
