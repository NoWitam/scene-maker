<?php

namespace src\Animations;

use src\Animations\Animation;

class Shake extends Animation
{
    protected float $strength = 1;
    
    protected function recipe() : array
    {
        return [
            10 => [
                "transform" => $this->getTransform(-1)
            ],
            20 => [
                "transform" => $this->getTransform(2)
            ],
            30 => [
                "transform" => $this->getTransform(-4)
            ],
            40 => [
                "transform" => $this->getTransform(4)
            ],
            50 => [
                "transform" => $this->getTransform(-4)
            ],
            60 => [
                "transform" => $this->getTransform(4)
            ],
            70 => [
                "transform" => $this->getTransform(-4)
            ],
            80 => [
                "transform" => $this->getTransform(2)
            ],
            90 => [
                "transform" => $this->getTransform(-1)
            ]
        ];
    }

    private function getTransform(int $pixels) : string
    {
        $pixels *= $this->strength;
        return "translate3d({$pixels}px, 0, 0)";
    }

    public function strength(float $strength) : self
    {
        $this->strength = $strength;

        return $this;
    }
}