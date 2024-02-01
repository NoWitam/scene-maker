<?php

namespace src\Components\Enums;

use src\Component;

enum Event : string
{
    case START = "start";
    case END = "end";

    public function getTime(Component $component) : float
    {
        return match($this) {
            self::START => $component->getStart(),
            self::END => $component->getEnd(),
        };
    }
}