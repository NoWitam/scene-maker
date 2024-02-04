<?php

namespace src\Components;

use src\Components\Traits\HasRotate;
use src\Helper;
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
                ["src" => Helper::getImageSrc($this->url)
            ]),
            styles: $this->mergeStyles([])
        );
    }

    public function url(string $url) : self
    {
        $this->url = $url;

        return $this;
    }

    public function isValid() : bool
    {
        if(is_null($this->url)){
            return false;
        }

        return parent::isValid();
    }
}