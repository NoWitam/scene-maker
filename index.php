<?php

use src\Animations\Enums\AnimationDirection;
use src\Animations\Fade;
use src\Animations\Rotate;
use src\Animations\RotateDiagonal;
use src\Animations\Scale;
use src\Animations\Wiggle;
use src\Components\Enums\Event;
use src\Components\Enums\TextAlign;
use src\Components\Image;
use src\Components\Text;
use src\HtmlTags\CloseHtmlTag;
use src\HtmlTags\OpenHtmlTag;
use src\VideoEditor;

spl_autoload_register(function ($class_name) {
    $class_name = str_replace("\\", "/", $class_name);
    include $class_name . '.php';
});

try {

    // $tag = CloseHtmlTag::make(
    //     'img',
    //     ["class1", "class2"],
    //     ["att1key" => "att1value", "att2key" => "att2value"],
    //     ["width" => "200px", "height" => "300px"],
    //     "siema"
    // );

    // echo $tag->render();


    $a = new VideoEditor();
    $a->setFps(60);

    // $a->addChainComponents(0, 
    //     Image::make('image1')->setLength(5)->size(800, 450)->position(0, 0)->url("assets/img/roman_soliders.jpg"),
    //     Image::make('image2')->setLength(2)->size(800, 450)->url("assets/img/roman_soliders.jpg"),
    // );

    $a->addComponent(
        Text::make('text1')->setStart(0)->setLength(10)->size(400, 225)->position(100, 100, 1)
            ->text("Witamy w naszej bajce")
            ->fontSize(40)
            ->align(TextAlign::CENTER)
            ->color('white')
            ->stroke(5, 'green')
            ->attachStartingAnimation(
                Wiggle::make('animation1')->setLength(4)
            )
            ->attachStartingAnimation(
                Scale::make('animation2')->setLength(1)
            )
            // ->rotate(15)
    );

    // $a->addComponentRelative(
    //     Image::make('imageStart')->setLength(2)->setDelay(1)->position(200, 200)->size(800, 450)->url("assets/img/roman_soliders.jpg"),
    //     componentStartName: 'image1',
    //     startType: Event::END
    // );

   $a->showTime(0.6);

} catch( Throwable $e ) { 
    echo $e->getMessage() . "\n";
    echo $e->getFile() . " " . $e->getLine();
}
