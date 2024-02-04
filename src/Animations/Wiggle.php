<?php

namespace src\Animations;

use src\Animations\Animation;

class Wiggle extends Animation
{
    protected float $strength = 1;
    
    protected function recipe() : array
    {
        return [
            0 => [
                "transform" => $this->getTransform(0)
            ],
            7 => [
                "transform" => $this->getTransform(0)
            ],
            15 => [
                "transform" => $this->getTransform(-15)
            ],
            20 => [
                "transform" => $this->getTransform(10)
            ],
            25 => [
                "transform" => $this->getTransform(-10)
            ],
            30 => [
                "transform" => $this->getTransform(6)
            ],
            35 => [
                "transform" => $this->getTransform(-4)
            ],
            40 => [
                "transform" => $this->getTransform(0)
            ],
            100 => [
                "transform" => $this->getTransform(0)
            ]
        ];
    }

    private function getTransform(int $degree) : string
    {
        $degree *= $this->strength;
        return "rotateZ({$degree}deg)";
    }

    public function strength(float $strength) : self
    {
        $this->strength = $strength;

        return $this;
    }
}