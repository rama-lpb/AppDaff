<?php

namespace App\Core;

class Singleton
{
    private static  $instances = [];

    public static function getInstance():static
    {
        $calledClass = static::class;
        if(empty(self::$instance[$calledClass]))
        {
            self::$instances[$calledClass] = new static();
        }
        return self::$instances[$calledClass];
    }
}