<?php

namespace src\HtmlTags;

class OpenHtmlTag extends HtmlTag
{
    function __construct(
        protected string $tag,
        protected array $classes = [],
        protected array $attributes = [],
        protected array $styles = [],
        protected string $content = ""
    ) {}

    public function render() : string
    {
        $classes = implode(" ", $this->classes);

        $attributes = implode(" ", array_map(
            fn($key, $value) => "{$key}=\"{$value}\"",
            array_keys($this->attributes),
            $this->attributes
        ));

        $styles = implode(" ", array_map(
            fn($key, $value) => "{$key}: {$value};",
            array_keys($this->styles),
            $this->styles
        ));

        return
        "<{$this->tag} 
            class=\"{$classes}\"
            {$attributes}
            style=\"{$styles}\"
        > {$this->content} </{$this->tag}>";
    }

}