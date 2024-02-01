<?php

namespace src\Components\Enums;

use src\Interfaces\CanHaveTime;

enum ComponentEvent : string
{
    case START = "start";
    case END = "end";

    public function getTime(CanHaveTime $component) : float
    {
        return match($this) {
            self::START => $component->getStart(),
            self::END => $component->getEnd(),
        };
    }

    public function setTime(CanHaveTime $component, $time)
    {
        match($this) {
            self::START => $component->setStart($time),
            self::END => $component->setEnd($time),
        };
    }
}