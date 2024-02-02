<?php

namespace src\Animations;

use src\Animations\Animation;

class Scale extends Animation
{
    protected float $scale = 1.5;

    protected function recipe() : array
    {
        return [
            0 => [
                "transform" => "scale(1)"
            ],
            100 => [
                "transform" => "scale({$this->scale})"
            ]
        ];
    }

    public function scale(float $scale) : self
    {
        $this->scale = $scale;

        return $this;
    }
}