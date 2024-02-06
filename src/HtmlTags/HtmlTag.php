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

    abstract public function render(?float $time) : string;

    public function mergeClasses(array $classes) : self
    {
        $this->classes = array_merge($this->classes, $classes);

        return $this;
    }

    public function mergeAttributes(array $attributes) : self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        
        return $this;
    }

    public function mergeStyles(array $styles) : self
    {
        $this->styles = array_merge($this->styles, $styles);
        
        return $this;
    }

    public function setTag(string $tag) : self
    {
        $this->tag = $tag;

        return $this;
    }

    public function setClasses(array $classes) : self
    {
        $this->classes = $classes;

        return $this;
    }

    public function setAttributes(array $attributes) : self
    {
        $this->attributes = $attributes;
        
        return $this;
    }

    public function setStyles(array $styles) : self
    {
        $this->styles = $styles;
        
        return $this;
    }



    function __toString() : string 
    {
        return $this->render(null);
    } 

}