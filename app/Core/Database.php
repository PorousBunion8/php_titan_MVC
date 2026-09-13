<?php
// conexao com o banco de dados usando pdo e singleton para manter uma unica conexao

namespace App\Core;

use PDO;
use PDOException;

//========================================================
//= Conexao singleton com banco de dados via pdo
//========================================================

class Database
{
    private static ?PDO $instance = null;

    // Construtor privado para evitar multiplas instancias
    private function __construct() {}
    private function __clone() {}

    // Retorna a instancia unica de conexao pdo, a base do PDO
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    DB_HOST,
                    DB_PORT,
                    DB_NAME,
                    DB_CHARSET
                );

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die('Erro de conexao com o banco: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
