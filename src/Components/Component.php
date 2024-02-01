<?php

namespace src\Components;

use Exception;
use src\Animations\Animation;
use src\Animations\Rotate;
use src\Components\Enums\ComponentEvent;
use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;
use src\Interfaces\CanHaveTime;
use src\Traits\HasCallableTraits;
use src\Traits\HasTime;

abstract class Component implements CanHaveTime
{
    use HasTime, HasCallableTraits;
    protected ?int $width = null;
    protected ?int $height = null;
    protected int $x = 0;
    protected int $y = 0;
    protected int $z = 0;
    protected $animations = [];

    function __construct(
        protected string $name
    ) {}

    public static function make(string $name) : self
    {
        return new static($name);  
    }

    public function render(float $time): string
    {
        $animations = count($this->animations) == 0 ? "zerio" : 
        OpenHtmlTag::make(
            tag: 'div',
            classes: ['animations'],
            content: implode("\n", array_map(
                function ($animation) use ($time) { 
                    return $animation->render($time, $this->name);
                },
                $this->animations
            ))
        );

        return OpenHtmlTag::make(
            tag: 'div',
            classes: ["container"],
            attributes: ["name" => $this->name],
            styles: [
                'height' => $this->height . "px",
                'width' => $this->width . "px",
                'top' => $this->y . "px",
                'left' => $this->x . "px",
                'z-index' => $this->z,
                ...$this->animationStyle($time)
            ],
            content: $this->tag($time) . $animations
        );
    }

    abstract public function tag(float $time) : HtmlTag;

    public function size(int $height, int $width) : self
    {
        $this->height = $height;
        $this->width = $width;

        return $this;
    }

    public function position(int $x, int $y, int $z = 0) : self
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;

        return $this;
    }


    public function getName() : string
    {
        return $this->name;
    }

    public function getLength() : float
    {
        return $this->end - $this->start;
    }

    public function isValid() : bool
    {
        $returns = $this->callMethodInTraits('isValid'); 

        return !in_array(false, $returns);
    }

    public function attachAnimation(Animation $animation) : self
    {
        if($animation->isValid()) {
            if(isset($this->animations[$animation->getName()])) {
                throw new Exception("Animation {$animation->getName()} already exist on component {$this->name}");
            }
            $this->animations[$animation->getName()] = $animation;
        } else {
            throw new Exception("Animation {$animation->getName()} is not valid on component {$this->name}");
        }

        return $this;
    }

    public function attachAnimationRelative(Animation $animation, ComponentEvent $event) : self
    {
        $event->setTime($animation, $event->getTime($this));

        return $this->attachAnimation($animation);
    }

    public function attachStartingAnimation(Animation $animation) : self
    {
        return $this->attachAnimationRelative($animation, ComponentEvent::START);
    }

    public function attachEndingAnimation(Animation $animation) : self
    {
        return $this->attachAnimationRelative($animation, ComponentEvent::END);
    }


    protected function mergeAttributes(array $componentAttributes) : array
    {        
        $returns = $this->callMethodInTraits('attributes'); 
        
        $traitsAttributes = [];
        foreach($returns as $retrun)
        {
            if(is_array($retrun)) {
                $traitsAttributes = array_merge($traitsAttributes, $retrun);
            } else {
                $traitsAttributes[] = $retrun;
            }
        }

        return array_merge($traitsAttributes, $componentAttributes);
    }

    protected function mergeStyles(array $componentStyles) : array
    {   
        $returns = $this->callMethodInTraits('styles'); 

        $traitsStyles = [];
        foreach($returns as $retrun)
        {
            if(is_array($retrun)) {
                $traitsStyles = array_merge($traitsStyles, $retrun);
            } else {
                $traitsStyles[] = $retrun;
            }
        }

        return array_merge($traitsStyles, $componentStyles);
    }

    private function animationStyle(float $time) : array
    {
        if(count($this->animations)) {
            $names = implode(", ", array_map(
                function($animation) {
                    return $this->getName() . "-" . $animation->getName();
                },
                $this->animations
            ));

            $durations = implode(", ", array_map(
                function($animation) {
                    return ($animation->getEnd() - $animation->getStart()) . "s";
                },
                $this->animations
            ));

            $delays = implode(", ", array_map(
                function($animation) use ($time) {
                    return ($animation->getStart() - $time) . "s";
                },
                $this->animations
            ));

            $fillModes = implode(", ", array_map(
                function($animation) {
                    return $animation->getFillMode();
                },
                $this->animations
            ));

            $timingFunctions = implode(", ", array_map(
                function($animation) {
                    return $animation->getTimingFunction();
                },
                $this->animations
            ));

            $directions = implode(", ", array_map(
                function($animation) {
                    return $animation->getDirection();
                },
                $this->animations
            ));

            $iterations = implode(", ", array_map(
                function($animation) {
                    return $animation->getDirection();
                },
                $this->animations
            ));

            return [
                "animation-name" => $names,
                "animation-duration" => $durations,
                "animation-delay" => $delays,
                "animation-fill-mode" => $fillModes,
                "animation-timing-function" => $timingFunctions,
                "animation-direction" => $directions,
                "animation-iteration-count" => $iterations,
            ];
        }

        return [];
    }
}