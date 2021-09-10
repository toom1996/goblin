<?php


namespace toom1996\base;


use toom1996\helpers\ConsoleHelper;

class BaseConsole 
{
    public static function consoleString(string $string, array $format)
    {
        return ConsoleHelper::ansiFormat($string, $format);
    }
}