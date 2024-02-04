<?php

namespace src\HtmlTags;

class EmptyHtmlTag extends HtmlTag
{
    function __construct() {
        parent::__construct("");
    }
    
    public function render(?float $time) : string
    {
        return "";
    }

}