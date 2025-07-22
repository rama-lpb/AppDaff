<?php 

namespace App\Core;

class Session
{
        private static ?Session $instance = null; 

    private function __construct()
    {
         if(session_status()===PHP_SESSION_NONE)
        {
            session_start();
        }
    }
    public static function getInstance()
    {
        if(self::$instance===null)
        {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    static public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

     public static function unset($key){
        unset($_SESSION[$key]);
    }

    public static function isset($key){
        return isset($_SESSION[$key]);

    }

    public function destroy(): void
    {
        session_unset();
        session_destroy();
        self::$instance = null;
    }
}