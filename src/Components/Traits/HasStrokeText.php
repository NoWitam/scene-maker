<?php

namespace src\Components\Traits;

trait HasStrokeText
{
    protected int $strokeSize = 0;
    protected ?string $strokeColor = null;

    public function stroke(int $size, string $color) : self
    {
        $this->strokeSize = $size;
        $this->strokeColor = $color;

        return $this;
    }

    public function attributesHasStrokeText(): array
    {
        return [
            "stroke-text" => $this->text
        ];
    }

    public function stylesHasStrokeText(): array
    {
        return [
            "--stroke-color" => $this->strokeColor,
            "--stroke-size" => $this->strokeSize . "px",
        ];
    }

}