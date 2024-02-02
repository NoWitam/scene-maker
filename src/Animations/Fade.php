<?php

namespace src\Animations;

use src\Animations\Animation;

class Fade extends Animation
{
    protected float $opacityStart = 0;
    protected float $opacityEnd = 1;

    protected function recipe() : array
    {
        return [
            0 => [
                "opacity" => "{$this->opacityStart}"
            ],
            100 => [
                "opacity" => "{$this->opacityEnd}"
            ]
        ];
    }

    public function opacity(float $start = 0, float $end = 1) : self
    {
        $this->opacityStart = $start;
        $this->opacityEnd = $end;

        return $this;
    }
}