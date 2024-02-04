<?php

namespace src\Interfaces;

interface CanHaveTime
{
    public function setLength(float $length) : self;

    public function setDelay(float $delay) : self;

    public function setStart(float $start) : self;

    public function setEnd(float $end) : self;

    public function getEnd() : ?float;

    public function getStart() : ?float;

    public function isValidHasTime() : bool;

}