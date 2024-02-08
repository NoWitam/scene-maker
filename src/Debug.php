<?php

namespace src;

abstract class Debug
{
    private static array $cache = [];

    public static function clear(): void
    {
        self::$cache = [];
    }

    public static function push(array $data): void
    {
        self::$cache[] = $data;
    }

    public static function merge(array $data): void
    {
        if(count(self::$cache) == 0) {
            self::$cache[] = $data;
            return;
        }

        $index = count(self::$cache) - 1;
        self::$cache[$index] = array_merge(self::$cache[$index], $data);
    }

    public static function get() : array
    {
        return self::$cache[count(self::$cache) -1];
    }

    public static function getAll() : array
    {
        return self::$cache;
    }

}