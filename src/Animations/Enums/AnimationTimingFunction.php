<?php

namespace src\Animations\Enums;

enum AnimationTimingFunction : string
{
    case EASE = "ease";
    case LINEAR = "linear";
    case EASE_IN = "ease-in";
    case EASE_OUT = "ease-out";
    case EASE_IN_OUT = "ease-in-out";
}