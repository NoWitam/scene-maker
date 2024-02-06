<?php

namespace src\Components;

use src\Components\Traits\HasStrokeText;
use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;

class SubText extends Component
{
    use HasStrokeText;
    private array $styles = [];
    private string $text;
    protected bool $isBlock = false;

    public function tag(float $time) : HtmlTag
    {
        return OpenHtmlTag::make(
            tag: 'a',
            attributes: $this->mergeAttributes([
                "stroke-text" => $this->text
            ]),
            content: $this->text,
        );
    }

    public function render(?float $time): HtmlTag
    {
        $tag = parent::render($time);

        return $tag->setTag('span')
                ->setStyles(
                    $this->mergeStyles($this->styles)
                )->setClasses([]);
    }

    public function styles(array $styles) : self
    {
        $this->styles = $styles;

        return $this;
    }

    public function text(string $text) : self
    {
        $this->text = $text;

        return $this;
    }
}