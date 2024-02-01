<?php

namespace src\Components;

use src\Component;
use src\Components\Traits\HasRotate;
use src\HtmlTags\CloseHtmlTag;
use src\HtmlTags\HtmlTag;

class Image extends Component
{
    use HasRotate;

    private ?string $url = null;

    public function tag(float $time) : HtmlTag
    {
        return CloseHtmlTag::make(
            tag: 'img',
            attributes: $this->mergeAttributes(
                ["src" => $this->url
            ]),
            styles: $this->mergeStyles([])
        );
    }

    public function url($url) : self
    {
        $this->url = $url;

        return $this;
    }
}