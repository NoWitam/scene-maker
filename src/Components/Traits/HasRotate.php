<?php

namespace src\Components\Traits;

trait HasRotate
{
    protected float $rotate = 0;

    public function rotate(int $degrees): self
    {
        $this->rotate = $degrees;

        return $this;
    }

    public function stylesHasRotate(): array
    {
        return [
            "transform" => "rotate({$this->rotate}deg)"
        ];
    }

}