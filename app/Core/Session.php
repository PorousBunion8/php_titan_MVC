<?php
// gerenciamento de sessao do usuario e mensagens flash de alerta

namespace App\Core;

//========================================================
//= Gerenciamento de sessoes e mensagens temporarias
//========================================================

class Session
{

    // Inicializa a sessao nativa do php
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Salva uma chave na sessao
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    // Recupera uma chave da sessao
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    // Verifica se a chave existe na sessao
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    // Remove uma chave da sessao
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    // Destroi a sessao atual
    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }

    // Define uma mensagem flash para a proxima requisicao
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'][$type] = $message;
    }

    // Recupera e remove a mensagem flash da sessao
    public static function getFlash(string $type): ?string
    {
        self::start();
        if (isset($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }
}
