<?php

namespace src\Components\Enums;

enum PlayerState : string
{
    case REPEAT = "repeat";
    case REVERSE_REPEAT = "reverse_repeat";
    case PLACEHOLDER  = "placeholder";
}