<?php

namespace src\Animations\Enums;

enum AnimationFillMode : string
{
    case NONE = "none";
    case FORWARDS = "forwards";
    case BACKWARDS = "backwards";
    case BOTH = "both";
}