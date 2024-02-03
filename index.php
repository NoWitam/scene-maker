<?php

use src\Animations\Scale;
use src\Animations\Wiggle;
use src\Components\Enums\ComponentEvent;
use src\Components\Enums\TextAlign;
use src\Components\Image;
use src\Components\Text;
use src\VideoEditor;

require 'vendor/autoload.php';

//echo realpath("assets/img/roman_soliders.jpg");
try {
    $heigt = 1920;
    $width = 1080;
    $a = new VideoEditor(1920, 1080);
    $a->setFps(24);

    $a->addChainComponents(0, 
        Image::make('image1')->setLength(7)->size($heigt, $width)->position(0, 0)->url(asset("img/roman_soliders.jpg")),
        //Image::make('image2')->setLength(2)->size(800, 450)->position(100, 100)->url(asset("img/roman_soliders.jpg")),
    );

    $a->addComponentParallel(
        Text::make('text1')->setStart(0)->setLength(7)->size(400, 225)->position(100, 100, 1)
            ->text("Witamy w naszej bajce")
            ->fontSize(40)
            ->align(TextAlign::CENTER)
            ->color('white')
            ->stroke(5, 'green')
            ->attachStartingAnimation(
                Wiggle::make('animation1')->setLength(4)->setDelay(2)
            )
            ->attachStartingAnimation(
                Scale::make('animation2')->setLength(6)
            )
            ->rotate(15),
        'image1'
    );

    // $a->addComponentRelative(
    //     Image::make('imageStart')->setLength(2)->setDelay(1)->position(200, 200)->size(200, 112)->url(asset("img/roman_soliders.jpg")),
    //     componentStartName: 'image1',
    //     startEvent: ComponentEvent::END
    // );

    $startTime = microtime(true);

    echo sprintf('%s.%03d', date('H:i:s'), floor(microtime(true) * 1000) % 1000) . "\n";
    //echo $a->showFrame(1);
    $a->run(__DIR__ . "/tmp/", "output/wideo");
    //$a->showTime(3);
    echo sprintf('%s.%03d', date('H:i:s'), floor(microtime(true) * 1000) % 1000) . "\n";

    $executionTime = microtime(true) - $startTime;
    echo "Trwało to {$executionTime} sekund \n";

} catch( Throwable $e ) { 
    echo $e->getMessage() . "\n";
    echo $e->getFile() . " " . $e->getLine();
}


function asset(string $path) : string
{
    return "http://localhost:3000/assets/" . $path;
}