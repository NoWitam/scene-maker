<?php

namespace src\Animations;
use src\Animations\Animation;

class Rotate extends Animation
{
    protected float $degree = 0;

    protected function recipe() : array
    {
        return [
            0 => [
                "rotate" => "0deg"
            ],
            100 => [
                "rotate" => "{$this->degree}deg"
            ]
        ];
    }

    public function degree(float $degree) : self
    {
        $this->degree = $degree;

        return $this;
    }
}