<?php

namespace src\Animations;

use src\Animations\Animation;

class RotateDiagonal extends Animation
{
    protected float $degree = 360;

    protected function recipe() : array
    {
        return [
            0 => [
                "rotate" => "1 2 1 0deg"
            ],
            100 => [
                "rotate" => "1 2 1 {$this->degree}deg"
            ]
        ];
    }

    public function degree(float $degree) : self
    {
        $this->degree = $degree;

        return $this;
    }
}