<?php

namespace src;

use Exception;
use src\Animations\Animation;
use src\Components\Enums\ComponentEvent;
use src\Components\Component;

class VideoEditor
{
    private string $background = "#FFFFFF";
    private int $fps = 60;
    private array $timeline = [];
    private array $components = [];
    private int $frame = 0;

    public function setBackground(string $background)
    {
        $this->background = $background;
    }

    public function setFps(int $fps)
    {
        $this->fps = $fps;
    }

    public function getComponent($componentName) : Component
    {
        if(isset($this->components[$componentName])) {
            return $this->components[$componentName];
        }

        throw new Exception("Component {$componentName} not exist");
    }

    public function addChainComponents(float $start, Component ...$components) : self
    {
        foreach ($components as $component)
        {
            $component->setStart($start);
            $this->addComponent($component);

            $start = $component->getEnd();
        }

        return $this;
    }

    public function addComponents(Component ...$components) : self
    {
        foreach ($components as $component)
        {
            $this->addComponent($component);
        }

        return $this;
    }

    public function addComponentRelative(Component $component, ?string $componentStartName = null, ?string $componentEndName = null, ComponentEvent $startEvent = ComponentEvent::START, ComponentEvent $endEvent = ComponentEvent::END) : self
    {
        if(isset($componentStartName)) {
            $startComponent = $this->getComponent($componentStartName);
            $component->setStart($startEvent->getTime($startComponent));
        }

        if(isset($componentEndName)) {
            $endComponent = $this->getComponent($componentEndName);
            $component->setEnd($endEvent->getTime($endComponent));
        }

        return $this->addComponent($component);
    }

    public function addComponentParallel(Component $component, string $componentName)
    {
        return $this->addComponentRelative($component, $componentName, $componentName);
    }

    public function addComponent(Component $component) : self
    {
        if($component->isValid()) {
            if(isset($this->components[$component->getName()])) {
                throw new Exception("Component {$component->getName()} already exist");
            }
            $this->components[$component->getName()] = $component;
        } else {
            throw new Exception("Component {$component->getName()} is not valid");
        }

        return $this;
    }

    public function getComponentLength(string $componentName) : float
    {
        return $this->getComponent($componentName)->getLength();
    }

    public function getComponentTime(string $componentName, ComponentEvent $event) : float
    {
        return $event->getTime($this->getComponent($componentName));
    }

    public function getComponentTimes(string $componentName, ComponentEvent ...$events) : array
    {
        return array_combine(
            $events,
            array_map(
                function (ComponentEvent $event) use ($componentName) {
                    return $event->getTime($this->getComponent($componentName));
                }, $events
            )
        );
    }

    public function attachAnimationToComponent(string $componentName, Animation $animation) : self
    {
        $this->getComponent($componentName)->attachAnimation($animation);

        return $this;
    }

    public function attachAnimationRelativeToComponent(string $componentName, Animation $animation, ComponentEvent $event) : self
    {
        $this->getComponent($componentName)->attachAnimationRelative($animation, $event);
        
        return $this;
    }

    public function attachStartingAnimationToComponent(string $componentName, Animation $animation) : self
    {
        $this->getComponent($componentName)->attachStartingAnimation($animation);

        return $this;
    }

    public function attachEndingAnimationToComponent(string $componentName, Animation $animation) : self
    {
        $this->getComponent($componentName)->attachEndingAnimation($animation);

        return $this;
    }

    public function attachAnimationCustomToComponent(
        string $componentName, Animation $animation, string $componentStartName, ComponentEvent $startEvent, string $componentEndName, ComponentEvent $endEvent
    ) : self
    {
        $this->getComponent($componentName)->attachAnimation(
            $animation
                ->setStart($startEvent->getTime($this->getComponent($componentStartName)))
                ->setEnd($endEvent->getTime($this->getComponent($componentEndName)))
        );

        return $this;
    }
    
    private function generateTimeline() 
    {
        $this->timeline = [];
        
        foreach($this->components as $component)
        {
            if(isset($this->timeline[$component->getStart()])) {
                $this->timeline[$component->getStart()][] = $component;
            } else {
                $this->timeline[$component->getStart()] = [$component];
            }
        }

        ksort($this->timeline);
    }

    private function getTime() : float
    {
        return $this->frame / $this->fps;
    }

    public function run()
    {
        $this->generateTimeline();
        foreach($this->generateFrame() as $frame)
        {
            echo $frame . "\n";
        }
    }

    public function showTime(float $time)
    {
        $this->generateTimeline();
        foreach($this->generateFrame() as $frame)
        {
            if($time < $this->getTime()) {
                echo $frame;
                break;
            }
        }
    }

    public function showFrame(int $frameCount)
    {
        $this->generateTimeline();
        foreach($this->generateFrame() as $frame)
        {
            if($this->frame == $frameCount) {
                echo $frame;
                break;
            }
        }
    }

    private function generateFrame()
    {
        while(count($this->timeline))
        {
            $html = '<link rel="stylesheet" href="assets/style.css"><body><div id="screen" style="background: ' . $this->background . '">';

            foreach($this->timeline as $time => $components)
            {
                if($time > $this->getTime()) {
                    break;
                }

                foreach($components as $i => $component)
                {
                    if($component->getEnd() <= $this->getTime()) {
                        unset($components[$i]);
                        if(count($components) == 0) {
                            unset($timeline[$time]);
                        }
                        break;
                    }

                    $html .= $component->render($this->getTime());
                }
            }

            $html .= "</div></body>";
            $this->frame++;
            yield $html;
        }
    }
}