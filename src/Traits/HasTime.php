<?php

namespace src\Traits;

trait HasTime
{
    private ?float $start = null;
    private ?float $end = null;
    private ?float $length = null;
    private float $delay = 0;

    public function setLength(float $length) : self
    {
        $this->length = $length;
        if(isset($this->start)) {
            $this->end = $this->start + $this->length;
        } else {
            if(isset($this->end)) {
                $this->start = $this->end - $this->length;
            }
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

    public function getEnd() : float
    {
        return $this->end;
    }

    public function getStart() : float
    {
        return $this->start;
    }

    public function isValidHasTime() : bool
    {
        return isset($this->start) AND isset($this->end) ? true : false;
    }
}