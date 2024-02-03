<?php

use src\Animations\Scale;
use src\Animations\Wiggle;
use src\Components\Enums\ComponentEvent;
use src\Components\Enums\TextAlign;
use src\Components\Image;
use src\Components\Text;
use src\Helper;
use src\VideoEditor;

require 'vendor/autoload.php';

//echo realpath("assets/img/roman_soliders.jpg");
try {
    $heigt = 1920;
    $width = 1080;
    $a = new VideoEditor($heigt, $width);
    $a->setFps(24);

    $a->addChainComponents(0, 
        Image::make('image1')->setLength(7)->size($heigt, $width)->url(Helper::asset("img/roman_soliders.jpg")),
        //Image::make('image2')->setLength(2)->size(800, 450)->position(100, 100)->url(Helper::asset("img/roman_soliders.jpg")),
    );

    $a->addComponentParallel(
        Text::make('text1')->setStart(0)->setLength(7)->size(400, 225)->position(100, 100, 1)
            ->text("Witamy w naszej bajce")
            ->fontSize(40)
            ->align(TextAlign::CENTER)
            ->color('white')
            ->stroke(5, 'green')
            // ->attachStartingAnimation(
            //     Wiggle::make('animation1')->setLength(4)->setDelay(2)
            // )
            // ->attachStartingAnimation(
            //     Scale::make('animation2')->setLength(6)
            // )
            // ->rotate(15)
        ,'image1'
    );

    $a->setPositionRelativeToScreen(
        'image1',
        screenVerticalProcent: 50
    );

    $a->setPositionRelativeToComponent(
        'text1',
        'image1',
        componentHorizontalProcent: 50,
        refernceHorizontalProcent: 50,
        verticalOffset: 20
    );

    // $a->addComponentRelative(
    //     Image::make('imageStart')->setLength(2)->setDelay(1)->position(200, 200)->size(200, 112)->url(Helper::asset("img/roman_soliders.jpg")),
    //     componentStartName: 'image1',
    //     startEvent: ComponentEvent::END
    // );

    $startTime = microtime(true);
 
    $a->showFrame(1, __DIR__ . "/output/test");
    //$a->run(__DIR__ . "/tmp/", "output/wideo");
    //$a->showTime(3);

    $executionTime = microtime(true) - $startTime;
    echo "Trwało to {$executionTime} sekund \n";

} catch( Throwable $e ) { 
    echo $e->getMessage() . "\n";
    echo $e->getFile() . " " . $e->getLine();
}