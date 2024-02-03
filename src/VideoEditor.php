<?php

namespace src;

use Exception;
use HeadlessChromium\BrowserFactory;
use Spatie\Browsershot\Browsershot;
use src\Animations\Animation;
use src\Components\Enums\ComponentEvent;
use src\Components\Component;
use src\Interfaces\CanHaveTime;

class VideoEditor
{
    private string $background = "#FFFFFF";
    private int $fps = 60;
    private array $timeline = [];
    private array $components = [];
    private int $frame = 0;

    function __construct(
        private int $height,
        private int $width
    ){}

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

    public function run(string $tmp, string $videoName)
    {
        $files = glob($tmp . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->generateTimeline();

        $browserFactory = new BrowserFactory("chromium");
        $browser = $browserFactory->createBrowser([
            'windowSize'   => [$this->width, $this->height],
        ]);

        try {
            $page = $browser->createPage();
            foreach($this->generateFrame() as $frame)
            {
                $frameNumber = str_pad($this->frame, 6, '0', STR_PAD_LEFT);

                $page->setHtml($frame);

                $page->screenshot([
                    'format'  => 'jpeg',
                    'quality' => 100,
                    'optimizeForSpeed' => false 
                ])->saveToFile("{$tmp}/frame_{$frameNumber}.jpeg");
            }     
        } finally {
            $browser->close();
        }


        exec("ffmpeg -r {$this->fps} -y -i '{$tmp}/frame_%06d.jpeg' '{$videoName}.mp4'");
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

    public function getLength() : float
    {
        return max(array_map(
            function (CanHaveTime $component) {
                return $component->getEnd();
            },
            $this->components
        ));
    }

    private function generateFrame()
    {
        while(true)
        {
            $renderComponents = [];
        
            $html = '<head><link rel="stylesheet" href="http://localhost:3000/assets/style.css"><body><div id="screen" style="background: ' . $this->background . '"></head><body>';

            foreach($this->timeline as $time => $components)
            {
                if($time > $this->getTime()) {
                    break;
                }

                foreach($components as $i => $component)
                {
                    if($component->getEnd() <= $this->getTime()) {
                        unset($this->timeline[$time][$i]);
                        if(count($this->timeline[$time]) == 0) {
                            unset($this->timeline[$time]);
                        }
                    } else {
                        $renderComponents[] = $component->getName();
                        $html .= $component->render($this->getTime());
                    }
                }
            }

            echo 
                "Frame: ". str_pad($this->frame+1, 6, '0', STR_PAD_LEFT) . 
                " | Procent: " . str_pad(round($this->getTime() / $this->getLength() * 100), 2, '0', STR_PAD_LEFT) . "%" .
                " | Time: " . str_pad(number_format($this->getTime(), 5, '.', ''), 9, '0', STR_PAD_LEFT) . "s " .
                " | Componentes: " .
                implode(", ", $renderComponents)
                . "\n";
            $html .= "</body>";

            if(count($this->timeline) == 0) {
                return;
            }

            $this->frame++;
            yield $html;
        }
    }
}