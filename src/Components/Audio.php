<?php

namespace src\Components;

use src\Helper;
use src\HtmlTags\HtmlTag;
use src\Components\Interfaces\Soundable;
use src\HtmlTags\EmptyHtmlTag;
use src\Sound;

class Audio extends Component implements Soundable
{
    private ?string $path = null;
    private ?float $duration = null;
    private float $speed = 1;

    public function tag(float $time) : HtmlTag
    {
        return EmptyHtmlTag::make();
    }

    public function path(string $path) : self
    {
        $this->path = $path;
        $this->duration = Helper::getFileDuration($path);

        return $this;
    }

    public function speed(float $speed) : self
    {
        $this->speed = $speed;

        return $this;
    }

    public function isValid() : bool
    {
        if(is_null($this->path)){
            return false;
        }

        if(is_null($this->getEnd()) AND !is_null($this->getStart())) {
            $this->setEnd($this->getStart() + $this->duration / abs($this->speed));
        }

        if(is_null($this->getStart()) AND !is_null($this->getEnd())) {
            $this->setStart($this->getEnd() - $this->duration / abs($this->speed));
        }

        return parent::isValid();
    }

    public function declareSound() : array 
    {
        return [
            new Sound($this->path, $this->getStart(), $this->getEnd(), $this->speed)
        ];
    }
}