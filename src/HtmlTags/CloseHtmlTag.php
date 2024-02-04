<?php

namespace src\HtmlTags;

class CloseHtmlTag extends HtmlTag
{
    public function render(?float $time) : string
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
        >";
    }

}