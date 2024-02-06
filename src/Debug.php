<?php

namespace src;

abstract class Debug
{
    private static ?int $time = null;  
    private static array $storage = [];
    private static array $calculator = [];

    public static function startTimer() : void
    {
        self::$time = microtime(true);
    }

    public static function print(callable $callback) : void
    {
        if(is_null(self::$time)) {
            print $callback(microtime(true));
            return;
        }

        print $callback(self::$time);
        self::$time = null;
    }

    public static function pasteToStorage(callable $callback) : void
    {
        self::$storage[0] = $callback;
    }

    public static function printFromStorage() : void
    {
        if(is_null(self::$time)) {
            print self::$storage[0](0);
            return;
        }

        print self::$storage[0](microtime(true) - self::$time);
        self::$time = null;
    }

    public static function pushToCalcultor($value) : void
    {
        self::$calculator[] = $value;
    }

    public static function calculate() : array
    {
        return [
            "avg" => array_sum(self::$calculator) / count(self::$calculator),
            "min" => min(self::$calculator),
            "max" => max(self::$calculator),
            "total" => array_sum(self::$calculator)
        ];
    }

    public static function clearCalcultor() : void
    {
        self::$calculator = [];
    }
}