<?php

namespace src\Interfaces;

interface Renderable
{
    public function render(?float $time): string;
}