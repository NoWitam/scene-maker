<?php

namespace src;

use Exception;
use src\Components\Enums\Event;
use src\Components\Component;

class VideoEditor
{
    private string $background = "#FFFFFF";
    private int $fps = 60;
    private array $timeline = [];
    private float $length = 0;
    private array $events = [];
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

    public function addComponentRelative(Component $component, ?string $componentStartName = null, ?string $componentEndName = null, Event $startType = Event::START, Event $endType = Event::END) : self
    {
        if(isset($componentStartName)) {
            $startComponent = $this->getComponent($componentStartName);
            $component->setStart($startType->getTime($startComponent));
        }

        if(isset($componentEndName)) {
            $endComponent = $this->getComponent($componentEndName);
            $component->setEnd($endType->getTime($endComponent));
        }

        return $this->addComponent($component);
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

    private function getTime() : float
    {
        return $this->frame / $this->fps;
    }

    public function run()
    {
        foreach($this->generateFrame() as $frame)
        {
            echo $frame . "\n";
        }
    }

    public function showFrame(float $time)
    {
        foreach($this->generateFrame() as $frame)
        {
            if($time < $this->getTime()) {
                echo $frame;
                break;
            }
        }
    }

    private function generateFrame()
    {
        $timeline = [];
        foreach($this->components as $component)
        {
            if(isset($timeline[$component->getStart()])) {
                $timeline[$component->getStart()][] = $component;
            } else {
                $timeline[$component->getStart()] = [$component];
            }
        }

        ksort($timeline);

        while(count($timeline))
        {
            $html = '<link rel="stylesheet" href="assets/style.css"><body><div id="screen" style="background: ' . $this->background . '">';

            foreach($timeline as $time => $components)
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



    /*
    function __toString()
    {
        return <<<GFG
            <link rel="stylesheet" href="assets/style.css">
            <div id="screen" style="background: $this->background">essa</div>
        GFG;
    }
    */

}