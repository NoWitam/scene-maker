<?php

namespace src\Animations;

use Error;
use src\Animations\Enums\AnimationDirection;
use src\Animations\Enums\AnimationFillMode;
use src\Animations\Enums\AnimationTimingFunction;
use src\Helper;
use src\HtmlTags\OpenHtmlTag;
use src\Interfaces\CanHaveTime;
use src\Traits\HasCallableTraits;
use src\Traits\HasTime;

abstract class Animation implements CanHaveTime
{
    use HasTime, HasCallableTraits;

    protected array $timeline = [];
    protected AnimationFillMode $fillMode = AnimationFillMode::FORWARDS;
    protected AnimationTimingFunction $timingFunction = AnimationTimingFunction::LINEAR;
    protected AnimationDirection $direction = AnimationDirection::NORMAL;
    protected ?int $iteration = 1; // null = "infinite"

    function __construct(
        protected string $name
    ) {}

    public static function make(string $name) : self
    {
        return new static($name);  
    }

    abstract protected function recipe() : array;

    public function render(string $parentName) : string
    {
        $content = "@keyframes {$parentName}-{$this->name} {";

        foreach($this->recipe() as $percents => $effects)
        {

            if(!Helper::isPercent($percents)) {
                throw new Error("Animation {$this->name} on component {$parentName} is not valid"); 
            }

            $percents .= "%";

            $content .= $percents . " { ";

            $content .= implode(" ", array_map(
                fn($key, $value) => "{$key}: {$value};",
                array_keys($effects),
                $effects
            ));

            $content .= "}";
        }

        $content .= "}";

        return OpenHtmlTag::make(
            tag: 'style',
            attributes: ["name" => $this->name],
            content: $content
        );
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function fillMode(AnimationFillMode $fillMode) : self
    {
        $this->fillMode = $fillMode;

        return $this;
    }

    public function getFillMode() : string
    {
        return $this->fillMode->value;
    }

    public function timingFunction(AnimationTimingFunction $timingFunction) : self
    {
        $this->timingFunction = $timingFunction;

        return $this;
    }

    public function getTimingFunction() : string
    {
        return $this->timingFunction->value;
    }

    public function direction(AnimationDirection $direction) : self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getDirection() : string
    {
        return $this->direction->value;
    }

    public function iteration(?int $iteration) : self
    {
        $this->iteration = $iteration;

        return $this;
    }

    public function getIteration() : int|string
    {
        return $this->iteration == null ? "infinite" : $this->iteration;
    }

    public function isValid() : bool
    {
        $returns = $this->callMethodInTraits('isValid'); 

        return !in_array(false, $returns);
    }
}