<?php

namespace src\Components\Traits;

trait HasRotate
{
    protected ?float $rotate = null;

    public function rotate(?int $degrees = null): self
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