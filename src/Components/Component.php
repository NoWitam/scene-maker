<?php

namespace src\Components;

use Exception;
use src\Animations\Animation;
use src\Components\Enums\ComponentEvent;
use src\HtmlTags\CloseHtmlTag;
use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;
use src\Interfaces\CanHaveTime;
use src\Traits\HasCallableTraits;
use src\Traits\HasTime;

abstract class Component implements CanHaveTime
{
    use HasTime, HasCallableTraits;
    protected ?int $width = 0;
    protected ?int $height = 0;
    protected int $x = 0;
    protected int $y = 0;
    protected int $z = 0;
    protected bool $isBlock = true;
    protected $animations = [];

    function __construct(
        protected string $name
    ) {}

    public static function make(string $name) : self
    {
        return new static($name);  
    }

    public function render(?float $time): HtmlTag
    {
        $tag = $this->isBlock ? "div" : "span";
        $animations = count($this->animations) == 0 ? "" : 
        OpenHtmlTag::make(
            tag: $tag,
            classes: ['animations'],
            content: implode("\n", array_map(
                function ($animation) { 
                    return $animation->render($this->name);
                },
                $this->animations
            ))
        );

        $animationsOpenLayer = "";
        $aniamtionCloseLayer = "";
        foreach($this->animations as $animation)
        {
            $animationsOpenLayer .= CloseHtmlTag::make(
                tag: $tag,
                classes: ['animation'],
                styles: $this->animationStyle($animation, $time)
            );
            $aniamtionCloseLayer = "</{$tag}>";
        }

        return OpenHtmlTag::make(
            tag: 'div',
            classes: ["container"],
            attributes: ["name" => $this->name],
            styles: [
                'height' => $this->height . "px",
                'width' => $this->width . "px",
                'top' => $this->y . "px",
                'left' => $this->x . "px",
                'z-index' => $this->z
            ],
            content: $animations . $animationsOpenLayer . $this->tag($time) . $aniamtionCloseLayer
        );
    }

    abstract public function tag(float $time) : HtmlTag;

    public function size(int $height, int $width) : self
    {
        $this->height = $height;
        $this->width = $width;

        return $this;
    }
    public function getWidth() : int
    {
        return $this->width;
    }

    public function getHeight() : int
    {
        return $this->height;
    }

    public function position(int $x, int $y, int $z = 0) : self
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;

        return $this;
    }

    public function positionX(int $x) : self
    {
        $this->x = $x;

        return $this;
    }

    public function positionY(int $y) : self
    {
        $this->y = $y;

        return $this;
    }

    public function positionZ(int $z) : self
    {
        $this->z = $z;

        return $this;
    }

    public function getPosition() : array
    {
        return [
            "x" => $this->x,
            "y" => $this->y,
            "z" => $this->z
        ];
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

    private function animationStyle(Animation $animation, float $time) : array
    {
        $name = $this->getName() . "-" . $animation->getName();
        $duration = ($animation->getEnd() - $animation->getStart()) . "s";
        $delay = ($animation->getStart() - $time) . "s";

        return [
            "animation-name" => $name,
            "animation-duration" => $duration,
            "animation-delay" => $delay,
            "animation-fill-mode" => $animation->getFillMode(),
            "animation-timing-function" => $animation->getTimingFunction(),
            "animation-direction" => $animation->getDirection(),
            "animation-iteration-count" => $animation->getIteration(),
        ];
    }
}