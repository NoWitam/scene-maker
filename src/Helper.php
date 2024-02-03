<?php

namespace src;

abstract class Helper
{
    public static function isPercent($value) : bool
    {
        if(!is_int($value) AND !is_float($value)) {
            return false;
        }

        if($value < 0) {
            return false;
        }

        if($value > 100) {
            return false;
        }

        return true;
    }
    public static function asset(string $path) : string
    {
        return "http://localhost:3000/assets/" . $path;
    }
}