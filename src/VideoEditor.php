<?php

namespace src;

use Exception;
use HeadlessChromium\BrowserFactory;
use InvalidArgumentException;
use src\Animations\Animation;
use src\Components\Enums\ComponentEvent;
use src\Components\Component;
use src\Interfaces\CanHaveTime;
use src\Interfaces\Prepareable;
use src\Components\Interfaces\Soundable;

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

    private function getComponent($componentName) : Component
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

    public function setPositionRelativeToScreen(
        string $componentName,
        int|float $componentHorizontalProcent = 0, int|float $componentVerticalProcent = 0,
        int|float $screenHorizontalProcent = 0, int|float $screenVerticalProcent = 0, 
        int $horizontalOffset = 0, int $verticalOffset = 0
    ) : self
    {
        return $this->setHorizontalPositionRelativeToScreen(
            $componentName, $componentHorizontalProcent, $screenHorizontalProcent, $horizontalOffset
        )->setVerticalPositionRelativeToScreen(
            $componentName, $componentVerticalProcent, $screenVerticalProcent,  $verticalOffset
        );
    }

    public function setHorizontalPositionRelativeToScreen(
        string $componentName, int|float $componentHorizontalProcent = 0, int|float $screenHorizontalProcent = 0, int $horizontalOffset = 0
    ) : self
    {
        if(!Helper::isPercent($screenHorizontalProcent) OR !Helper::isPercent($componentHorizontalProcent)) {
            throw new InvalidArgumentException("Wartość powinna być liczbą przedstawiającą procent");
        }

        $component = $this->getComponent($componentName);
        $component->positionX(
            ($this->width * $screenHorizontalProcent / 100) - ($component->getWidth() * $componentHorizontalProcent / 100) + $horizontalOffset
        );

        return $this;
    }

    public function setVerticalPositionRelativeToScreen(
        string $componentName, int|float $componentVerticalProcent = 0, int|float $screenVerticalProcent = 0, int $verticalOffset = 0
    ) : self
    {
        if(!Helper::isPercent($screenVerticalProcent) OR !Helper::isPercent($componentVerticalProcent)) {
            throw new InvalidArgumentException("Wartość powinna być liczbą przedstawiającą procent");
        }

        $component = $this->getComponent($componentName);
        $component->positionY(
            ($this->height * $screenVerticalProcent / 100) - ($component->getHeight() * $componentVerticalProcent / 100) + $verticalOffset
        );

        return $this;
    }

    public function setPositionRelativeToComponent(
        string $componentName, string $referenceName,
        int|float $componentHorizontalProcent = 0, int|float $componentVerticalProcent = 0,
        int|float $refernceHorizontalProcent = 0, int|float $refernceVerticalProcent = 0, 
        int $horizontalOffset = 0, int $verticalOffset = 0
    ) : self
    {
        return $this->setHorizontalPositionRelativeToComponent(
            $componentName, $referenceName, $componentHorizontalProcent, $refernceHorizontalProcent, $horizontalOffset
        )->setVerticalPositionRelativeToComponent(
            $componentName, $referenceName, $componentVerticalProcent, $refernceVerticalProcent,  $verticalOffset
        );
    }

    public function setHorizontalPositionRelativeToComponent(
        string $componentName, string $referenceName, int|float $componentHorizontalProcent = 0, int|float $refernceHorizontalProcent = 0, int $horizontalOffset = 0
    ) : self
    {
        if(!Helper::isPercent($refernceHorizontalProcent) OR !Helper::isPercent($componentHorizontalProcent)) {
            throw new InvalidArgumentException("Wartość powinna być liczbą przedstawiającą procent");
        }

        $component = $this->getComponent($componentName);
        $reference = $this->getComponent($referenceName);
        $component->positionX(
            ($reference->getPosition()['x'] + ($reference->getWidth() * $refernceHorizontalProcent / 100)) 
            - ($component->getWidth() * $componentHorizontalProcent / 100) 
            + $horizontalOffset
        );

        return $this;
    }

    public function setVerticalPositionRelativeToComponent(
        string $componentName, string $referenceName, int|float $componentVerticalProcent = 0, int|float $refernceVerticalProcent = 0, int $verticalOffset = 0
    ) : self
    {
        if(!Helper::isPercent($refernceVerticalProcent) OR !Helper::isPercent($componentVerticalProcent)) {
            throw new InvalidArgumentException("Wartość powinna być liczbą przedstawiającą procent");
        }

        $component = $this->getComponent($componentName);
        $reference = $this->getComponent($referenceName);
        $component->positionY(
            ($reference->getPosition()['y'] + ($reference->getHeight() * $refernceVerticalProcent / 100)) 
            - ($component->getHeight() * $componentVerticalProcent / 100) 
            + $verticalOffset
        );

        return $this;
    }
    
    private function getTime() : float
    {
        return $this->frame / $this->fps;
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

    public function generate(string $tmp, string $videoName)
    {
        $startTime = microtime(true);
        $extension = "mp4";

        Helper::rmdirr($tmp);
        mkdir($tmp);
      
        $directory = getcwd();
        chdir($tmp);

        $this->generateTimeline();

        $this->prepareComponents();

        $browserFactory = new BrowserFactory("chromium");
        $browser = $browserFactory->createBrowser([
            'windowSize'   => [$this->width, $this->height],
        ]);

        try {
            $page = $browser->createPage();

            foreach($this->generateFrames($tmp) as $frame)
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


        exec("ffmpeg -r {$this->fps} -y -i 'frame_%06d.jpeg' video.{$extension} 2>&1");

        $this->addSounds("video.{$extension}", $videoName . ".{$extension}");

        chdir($directory);

        $executionTime = microtime(true) - $startTime;
        echo "Trwało to {$executionTime} sekund \n";
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

    private function addSounds(string $videoWithoutMusicName, string $videoName)
    {
        $sounds = [];
        foreach($this->components as $component)
        {
            if($component instanceof Soundable) {
                $sounds = array_merge($sounds, $component->declareSound());
            }
        }

        $soundCount = count($sounds);

        if($soundCount) {
            $commands = array_map(
                function ($sound, $i) {
                    return $sound->command($i);
                },
                $sounds,
                range(1, $soundCount)
            );
    
            $names = array_column($commands, "name");
            $paths = array_column($commands, "path");
            $filters = array_column($commands, "filter");
    
            $command = "ffmpeg -y -i {$videoWithoutMusicName} " . implode(" ", $paths) . " " .
                "-filter_complex \"" . implode(";", $filters) . ";" . implode("", $names) . "amix={$soundCount}[a]" . "\"" .
                " -map 0:v -map \"[a]\" -preset ultrafast {$videoName}";
    
            exec($command . " 2>&1");
        } else {
            rename($videoWithoutMusicName, $videoName);
        }
    }

    private function generateFrames(string $tmp)
    {
        $runTimes = [];

        while(true)
        {
            $this->frame++;

            $startTime = microtime(true);

            $renderComponents = [];
        
            $html = '<head><link rel="stylesheet" href="http://localhost:3000/assets/style.css"><body><div id="screen" style="background: ' . $this->background . '"></head><body>';

            foreach($this->timeline as $time => $components)
            {
                if($time > $this->getTime()) {
                    break;
                }

                foreach($components as $i => $component)
                {
                    if($component->getEnd() < $this->getTime()) {
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

            $html .= "</body>";

            if(count($this->timeline) == 0) {
                print 
                    "Runtime - " .
                    "min: " . number_format(min($runTimes), 5, '.', '') . "s" . 
                    "| max: " . number_format(max($runTimes), 5, '.', '') . "s" .
                    "| average: " . number_format(array_sum($runTimes) / count($runTimes), 5, '.', '') . "s"
                    . "\n";

                return;
            }

            $runTime = microtime(true) - $startTime;
            $runTimes[$this->frame] = $runTime;

            print 
                "Frame: ". str_pad($this->frame, 6, '0', STR_PAD_LEFT) . 
                " | Procent: " . str_pad(round($this->getTime() / $this->getLength() * 100), 2, '0', STR_PAD_LEFT) . "%" .
                " | Time: " . str_pad(number_format($this->getTime(), 5, '.', ''), 9, '0', STR_PAD_LEFT) . "s " .
                " | RunTime: " . number_format($runTime, 5, '.', ''). "s " .
                " | Componentes: " .
                implode(", ", $renderComponents)
                . "\n";

            yield $html;
        }
    }

    public function showFrame(int $frameCount, string $tmp, string $frameName)
    {
        $directory = getcwd();
        chdir($tmp);

        $this->generateTimeline();

        $this->prepareComponents();

        foreach($this->generateFrames($tmp) as $frame)
        {
            if($this->frame == $frameCount) {
                $browserFactory = new BrowserFactory("chromium");
                $browser = $browserFactory->createBrowser([
                    'windowSize'   => [$this->width, $this->height],
                ]);
        
                try {
                    $page = $browser->createPage();

                    $page->setHtml($frame);
    
                    $page->screenshot([
                        'format'  => 'jpeg',
                        'quality' => 100,
                        'optimizeForSpeed' => false 
                    ])->saveToFile("{$frameName}.jpeg");

                } finally {
                    $browser->close();
                    $myfile = fopen("{$frameName}.html", "w");
                    fwrite($myfile, $frame);
                    fclose($myfile);
                    return;
                }
            }
        }

        chdir($directory);
    }
    private function prepareComponents()
    {
        $data = [
            'fps' => $this->fps,
            'legnth' => $this->getLength()
        ];

        foreach($this->components as $component)
        {
            if($component instanceof Prepareable){
                $component->prepare($data);
            }
        }
    }

}