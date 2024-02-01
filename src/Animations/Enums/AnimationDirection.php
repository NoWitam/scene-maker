<?php

namespace src\Animations\Enums;

enum AnimationDirection : string
{
    case NORMAL = "normal";
    case REVERSE = "reverse";
    case ALTERNATE = "alternate";
    case ALTERNATE_REVERSE = "alternate-reverse";
}