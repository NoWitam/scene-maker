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

    public static function getImageSrc(string $path) : string
    {
        $imageData = base64_encode(file_get_contents($path));
    
        return 'data: '. mime_content_type($path) . ';base64,' . $imageData;
    }

    public static function getFileDuration(string $path) : float 
    {
        return (float) exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$path} 2>&1");
    }

    public static function rmdirr(string $dirname)
    {
        // Sanity check
        if (!file_exists($dirname)) {
            return false;
        }

        // Simple delete for a file
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }

        // Loop through the folder
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Recurse
            self::rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }

        // Clean up
        $dir->close();
        return rmdir($dirname);
    }

    public static function timeString(float $seconds)
    {
        $time = floor($seconds);

        $ms = str_pad(
            round(($seconds - $time) * 1000)
            , 3, '0', STR_PAD_RIGHT
        );

        $hours = floor($time / 60 / 60);

        $time -= $hours * 60 * 60;

        $minutes = floor($time / 60);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        $time -= $minutes * 60;

        $seconds = str_pad($time, 2, '0', STR_PAD_LEFT);
    
        return "{$hours}h {$minutes}m {$seconds}s {$ms}ms";
    }
}