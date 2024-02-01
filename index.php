<?php

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
    $a->setFps(1);
    $a->addChainComponents(0, 
        Image::make('image1')->setLength(5)->size(800, 450)->position(0, 0)->url("assets/img/roman_soliders.jpg"),
        Image::make('image2')->setLength(2),
    );

    $a->addComponent(
        Text::make('text1')->setStart(4)->setLength(2)->size(400, 225)->position(100, 100)
            ->text("Witamy w naszej bajce")
            ->fontSize(40)
            ->align(TextAlign::CENTER)
            ->color('white')
            ->rotate(30)
            ->stroke(5, 'green')
    );

    $a->addComponentRelative(
        Image::make('imageStart')->setLength(2)->setDelay(1),
        componentStartName: 'image1',
        startType: Event::END
    );

   $a->showFrame(4);

} catch( Throwable $e ) { 
    echo $e->getMessage() . "\n";
    echo $e->getFile() . " " . $e->getLine();
}
