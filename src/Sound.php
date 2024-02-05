<?php

namespace src;

class Sound
{
    public function __construct(
        private string $path,
        private float $start,
        private float $end,
        private float $speed
    ){}

    public function command(int $i)
    {
        $tempo = abs($this->speed);
        $duration = ($this->end - $this->start) * $tempo;
        $delay = floor($this->start * 1000);
        $reverse = $this->speed < 0 ? "areverse, " : "";

        return [
            "path" => "-i {$this->path}",
            "name" => "[s{$i}]",
            "filter" => "[{$i}]{$reverse}atrim=0:{$duration}, atempo={$tempo}, adelay={$delay}|{$delay}[s{$i}]"
        ];
    }
}