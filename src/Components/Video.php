<?php

namespace src\Components;

use src\Components\Enums\PlayerState;
use src\Components\Interfaces\Soundable;
use src\Components\Traits\HasRotate;
use src\Helper;
use src\HtmlTags\CloseHtmlTag;
use src\HtmlTags\EmptyHtmlTag;
use src\HtmlTags\HtmlTag;
use src\Interfaces\Prepareable;
use src\Sound;

class Video extends Component implements Prepareable, Soundable
{
    use HasRotate;

    private ?string $url = null;
    private ?float $duration = null;
    private float $fps;
    private int $frameCount;
    private float $speed = 1;
    private HtmlTag $placeholder;
    private PlayerState $playerState = PlayerState::PLACEHOLDER;
    private bool $silent = false;

    function __construct(
        protected string $name
    ) {
        $this->placeholder = new EmptyHtmlTag();
        parent::__construct($name);
    }

    public function tag(float $time) : HtmlTag
    {
        $playTime = ($time - $this->getStart()) * abs($this->speed);
        $direction = $this->speed > 0 ? 1 : -1;
        $iteration = ceil($playTime / $this->duration) - 1;
        $playTime -= $iteration * $this->duration;

        if($iteration > 0) {

            if($this->playerState == PlayerState::PLACEHOLDER) {
                return $this->placeholder
                            ->mergeAttributes($this->mergeAttributes([]))
                            ->mergeStyles($this->mergeStyles([]));
            }

            if($this->playerState == PlayerState::REVERSE_REPEAT) {
                if($iteration % 2 == 1) {
                    $direction *= -1;
                }
            }
        }

        if($direction == -1) {
            $playTime = $this->duration - $playTime;
        }

        $frame = round(($playTime / $this->duration) * ($this->frameCount - 1)) + 1;

        $path = $this->name ."/frame_". str_pad($frame, 6, '0', STR_PAD_LEFT) .".bmp";

        return CloseHtmlTag::make(
            tag: 'img',
            attributes: $this->mergeAttributes(
                ["src" => Helper::getImageSrc($path)
            ]),
            styles: $this->mergeStyles([])
        );
    }

    public function url(string $url) : self
    {
        $this->url = $url;
        $this->duration = Helper::getFileDuration($url);
 
        return $this;
    }

    public function speed(float $speed) : self
    {
        $this->speed = $speed;

        return $this;
    }

    public function silent(bool $silent) : self
    {
        $this->silent = $silent;

        return $this;
    }

    public function playerState(PlayerState $playerState) : self
    {
        $this->playerState = $playerState;

        return $this;
    }

    public function placeholder(HtmlTag $placeholder) : self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function isValid() : bool
    {
        if(is_null($this->url)){
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

    public function prepare(array $data = []) : void
    {
        mkdir($this->name);

        $this->fps = $data['fps'] / abs($this->speed);
        
        exec("ffmpeg -i {$this->url} -filter:v fps=fps={$this->fps} {$this->name}/frame_%06d.bmp 2>&1");

        $this->frameCount = count(glob("{$this->name}/frame_*"));
  
        if(!$this->silent) {
            exec("ffmpeg -i {$this->url} -vn -acodec libmp3lame -q:a 0 {$this->name}/audio.mp3 2>&1");
        }
    }

    public function declareSound() : array
    {
        if($this->silent) {
            return [];
        }

        $sounds = [];
        $path = "{$this->name}/audio.mp3";
        $componentDuration = $this->getEnd() - $this->getStart();
        $videoDuration = $this->duration / abs($this->speed);

        if($this->playerState == PlayerState::PLACEHOLDER) {
            return [
                new Sound(
                    start: $this->getStart(),
                    end: $this->getStart() + $videoDuration,
                    path: $path,
                    speed: $this->speed
                )
            ];
        }

        for(
            $iteration=0;
            $iteration<ceil($componentDuration / $videoDuration);
            $iteration++
        ){
            $start = $this->getStart() + $iteration * $videoDuration;

            $sounds[] = new Sound(
                start: $start,
                end: $start + $videoDuration > $this->getEnd() ? $this->getEnd() : $start + $videoDuration,
                path: $path,
                speed: $this->playerState != PlayerState::REVERSE_REPEAT ? $this->speed : 
                    ($iteration % 2 == 0 ? $this->speed : $this->speed * -1)
            );
        }
  
        return $sounds;
    }
}