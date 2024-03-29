<?php

namespace src\Components;

use src\Components\Enums\TextAlign;
use src\Components\Enums\VerticalAlign;
use src\Components\Traits\HasRotate;
use src\Components\Traits\HasStrokeText;
use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;

class Text extends Component
{
    use HasRotate, HasStrokeText;

    protected ?string $text = null;
    protected ?int $fontSize = null;
    protected TextAlign $align = TextAlign::CENTER;
    protected VerticalAlign $verticalAlign = VerticalAlign::CENTER;
    protected ?string $color = null;
    protected ?int $wordSpacing = null;
    protected ?int $letterSpacing = null;
    protected ?int $lineHeight = null;

    public function tag(float $time) : HtmlTag
    {
        return OpenHtmlTag::make(
            tag: 'p',
            attributes: $this->mergeAttributes([]),
            content: $this->text,
        );
    }

    public function tagContainer(float $time, string $content) : HtmlTag|string
    {
        $tag = OpenHtmlTag::make(
            tag: 'div',
            content: $content,
            classes: ["textbox"],
            styles: $this->mergeStyles([
                'font-size' => $this->fontSize . "px",
                'text-align' => $this->align->value,
                'color' => $this->color,
                "align-items" => $this->verticalAlign->value,
                "line-height" => $this->lineHeight . "px",
                "letter-spacing" => $this->letterSpacing . "px",
                "word-spacing" => $this->wordSpacing . "px"
            ])
        );
        
        return $tag;
    }

    public function text($text) : self
    {
        $this->text = trim($text);

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

    public function verticalAlign(VerticalAlign $verticalAlign) : self
    {
        $this->verticalAlign = $verticalAlign;

        return $this;
    }

    public function color(string $color) : self 
    {
        $this->color = $color;

        return $this;
    }

    public function wordSpacing(int $wordSpacing) : self
    {
        $this->wordSpacing = $wordSpacing;

        return $this;
    }

    public function letterSpacing(int $letterSpacing) : self
    {
        $this->letterSpacing = $letterSpacing;

        return $this;
    }

    public function lineHeight(int $lineHeight) : self
    {
        $this->lineHeight = $lineHeight;

        return $this;
    }
}