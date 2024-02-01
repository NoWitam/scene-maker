<?php

namespace src;

use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;

abstract class Component
{
    protected ?float $start = null;
    protected ?float $end = null;
    protected ?float $length = null;
    protected ?int $width = null;
    protected ?int $height = null;
    protected float $delay = 0;
    protected int $x = 0;
    protected int $y = 0;

    function __construct(
        public string $name
    ) {}

    public static function make(string $name) : self
    {
        return new static($name);  
    }

    public function render(float $time): string
    {
        return OpenHtmlTag::make(
            tag: 'div',
            classes: ["container"],
            styles: [
                'height' => $this->height . "px",
                'width' => $this->width . "px",
                'top' => $this->y . "px",
                'left' => $this->x . "px"
            ],
            content: $this->tag($time)
        );
    }

    abstract public function tag(float $time) : HtmlTag;

    public function setLength(float $length) : self
    {
        $this->length = $length;
        if(isset($this->start)) {
            $this->end = $this->start + $this->length;
        }

        return $this;
    }

    public function setDelay(float $delay) : self
    {
        $this->delay = $delay;

        return $this;
    }

    public function setStart(float $start) : self
    {
        $this->start = $start + $this->delay;

        if(isset($this->length)) {
            $this->end = $this->start + $this->length;
        }

        return $this;
    }

    public function setEnd(float $end) : self
    {
        $this->end = $end;

        if(!isset($this->start) AND isset($this->length)) {
            $this->start = $this->end - $this->length;
        }

        return $this;
    }

    public function size(int $height, int $width) : self
    {
        $this->height = $height;
        $this->width = $width;

        return $this;
    }

    public function position(int $x, int $y) : self
    {
        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function isValid()
    {
        return isset($this->start) AND isset($this->end) ? true : false;
    }

    public function getName() : string
    {
        return $this->name;
    }

    protected function mergeAttributes(array $componentAttributes) : array
    {        
        $traitsAttributes = [];
        foreach(get_declared_traits() as $trait)
        {
            $trait = explode("\\", $trait);
            $trait = end($trait);

            $method = 'attributes' . $trait;

            if(method_exists($this, $method)) {
                $traitsAttributes = array_merge($traitsAttributes, $this->{$method}());
            }
        }

        return array_merge($traitsAttributes, $componentAttributes);
    }

    protected function mergeStyles(array $componentStyles) : array
    {        
        $traitsStyles = [];
        foreach(get_declared_traits() as $trait)
        {
            $trait = explode("\\", $trait);
            $trait = end($trait);

            $method = 'styles' . $trait;

            if(method_exists($this, $method)) {
                $traitsStyles = array_merge($traitsStyles, $this->{$method}());
            }
        }

        return array_merge($traitsStyles, $componentStyles);
    }
}