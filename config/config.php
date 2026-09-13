<?php
// arquivo central de configuracao com constantes de banco, fuso horario e caminhos do sistema

//========================================================
//= Configuracoes gerais 
//========================================================

// Fuso horario
date_default_timezone_set('America/Sao_Paulo');

// Caminhos base 
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/Views');

// Url base da aplicacao
define('BASE_URL', 'http://localhost:8000');

// Configuracoes do banco de dados mysql
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'jm_informatica');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
