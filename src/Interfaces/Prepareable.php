<?php

namespace src\Interfaces;

interface Prepareable
{
    public function prepare(array $data = []) : void;
}