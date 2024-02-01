<?php

namespace src\Components;

use src\Component;
use src\Components\Enums\TextAlign;
use src\Components\Traits\HasRotate;
use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;

class Text extends Component
{
    use HasRotate;

    private ?string $text = null;
    private ?int $fontSize = null;
    private ?TextAlign $align = null;
    private ?string $color = null;
    private int $strokeSize = 0;
    private ?string $strokeColor = null;

    public function tag(float $time) : HtmlTag
    {
        return OpenHtmlTag::make(
            tag: 'div',
            content: OpenHtmlTag::make(
                tag: 'p',
                attributes: $this->mergeAttributes([
                    "stroke-text" => $this->text
                ]),
                content: $this->text
            ),
            classes: ["textbox"],
            styles: $this->mergeStyles([
                'font-size' => $this->fontSize . "px",
                'text-align' => $this->align->value,
                'color' => $this->color,
                "--stroke-color" => $this->strokeColor,
                "--stroke-size" => $this->strokeSize . "px"
            ])
        );
    }

    public function text($text) : self
    {
        $this->text = $text;

        return $this;
    }

    public function fontSize($fontSize) : self
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    public function align(TextAlign $align) : self
    {
        $this->align = $align;

        return $this;
    }

    public function color(string $color) : self 
    {
        $this->color = $color;

        return $this;
    }

    public function stroke(int $size, string $color) : self
    {
        $this->strokeSize = $size;
        $this->strokeColor = $color;

        return $this;
    }
}