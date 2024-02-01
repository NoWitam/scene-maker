<?php

namespace src\HtmlTags;

abstract class HtmlTag
{
    function __construct(
        protected string $tag,
        protected array $classes = [],
        protected array $attributes = [],
        protected array $styles = []
    ) {}

    public static function make(...$params) : self
    {
        return new static(...$params);
    }

    abstract public function render() : string;

    function __toString() : string 
    {
        return $this->render();
    }

}