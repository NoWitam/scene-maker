<?php

use src\Animations\Scale;
use src\Animations\Shake;
use src\Animations\Wiggle;
use src\Components\Audio;
use src\Components\DecoratedText;
use src\Components\Enums\ComponentEvent;
use src\Components\Enums\PlayerState;
use src\Components\Enums\TextAlign;
use src\Components\Enums\VerticalAlign;
use src\Components\Image;
use src\Components\Text;
use src\Components\Video;
use src\Helper;
use src\HtmlTags\CloseHtmlTag;
use src\HtmlTags\OpenHtmlTag;
use src\VideoEditor;

require 'vendor/autoload.php';

try {

    $heigt = 1920;
    $width = 1080;

    $a = new VideoEditor($heigt, $width);

    $a->setFps(24);

    $a->addComponent(
        Image::make('image1')->setStart(0)->setLength(4)->size($heigt/2, $width)->url(__DIR__ . "/assets/img/roman_soliders.jpg")
        ->attachAnimation(Wiggle::make('anim_scale')->setStart(0)->setLength(1)->iteration(null))
    );

    $a->setPositionRelativeToScreen(
        'image1',
        screenVerticalProcent: 50
    );

    // $a->addComponent(
    //     Audio::make('sound1')->setStart(1)->path(__DIR__ . "/assets/audio/wiedzmin.mp3")->speed(-1)
    // );

    // $a->addComponent(
    //     Audio::make('sound2')->setStart(1)->path(__DIR__ . "/assets/audio/wiedzmin.mp3")->speed(1)
    // );

    // $a->addComponent(
    //     Video::make("video")->setStart(0)->size($heigt/2, $width)->url(__DIR__ . "/assets/video/wiedzmin.mp4")
    //     ->speed(1.5)
    //     ->playerState(PlayerState::REVERSE_REPEAT)
    //     ->setLength(4)
    // );

    // $a->addComponent(
    //     Audio::make('sound1')->setStart(1)->path(__DIR__ . "/assets/audio/wiedzmin.mp3")->speed(-1)
    // );

    // $a->addComponent(
    //     Audio::make('sound2')->setStart(1)->path(__DIR__ . "/assets/audio/wiedzmin.mp3")->speed(1)
    // )

    $a->addComponentParallel(
        DecoratedText::make('text1')->size($heigt/2, $width)->position(100, 100, 1)
            ->text("Witamy w naszej bajce RAIZHELL - PULL THE TRIGGER (PHONK)")
            ->setDelay(0)
            ->fontSize(84)
            ->align(TextAlign::CENTER)
            ->color('#ab2e7a')
            ->stroke(15, '#e9d5ea')
            ->verticalAlign(VerticalAlign::TOP)
            ->lineHeight(84)
            ->letterSpacing(20)
            // ->pushDecoratedStyle(3, [
            //     "transform" => "rotate(30deg)"
            // ])
            //->pushDecoratedAnimation(3, Scale::make('anim_scale')->setStart(0)->setLength(1))
            ->pushDecoratedAnimation(3, Shake::make('anim_shake')->setStart(0)->setLength(2)->strength(2))
            ->pushDecoratedAnimation(3, Wiggle::make('anim_wiggle')->setDelay(4)->setLength(2)->strength(2), ComponentEvent::START)
            ->wordSpacing(20)
        ,'image1'
    );

    $a->attachAnimationRelativeToComponent(
        'text1',
        Wiggle::make('animation1')->setLength(2)->setDelay(4),
        ComponentEvent::START
    );

    $a->attachAnimationRelativeToComponent(
        'text1',
        Scale::make('animation2')->setLength(6),
        ComponentEvent::START
    );


    $a->setPositionRelativeToComponent(
        'text1',
        'image1',
        componentHorizontalProcent: 50,
        refernceHorizontalProcent: 50,
        componentVerticalProcent: 50,
        refernceVerticalProcent: 50
    );

    $a->addComponentParallel(
        Text::make('text2')->size($heigt/2, $width)->position(100, 100, 1)
            ->text("Witamy w naszej bajce RAIZHELL - PULL THE TRIGGER (PHONK)")
            ->fontSize(84)
            ->align(TextAlign::CENTER)
            ->color('#ab2e7a')
            ->stroke(15, '#e9d5ea')
            ->verticalAlign(VerticalAlign::BOT)
            ->lineHeight(94)
            ->letterSpacing(30)
            // ->attachAnimation(Scale::make('anim_scale')->setStart(0)->setLength(1))
            // ->attachStartingAnimation(
            //     Wiggle::make('animation1')->setLength(4)->setDelay(2)
            // )
            // ->attachStartingAnimation(
            //     Scale::make('animation2')->setLength(6)
            // )
            // ->rotate(15)
        ,'image1'
    );

    $a->setPositionRelativeToComponent(
        'text2',
        'image1',
        componentHorizontalProcent: 50,
        refernceHorizontalProcent: 50,
        componentVerticalProcent: 50,
        refernceVerticalProcent: 50
    );

    // $a->addComponentRelative(
    //     Image::make('imageStart')->setLength(2)->setDelay(1)->position(200, 200)->size(200, 112)->url(__DIR__ . "/assets/img/roman_soliders.jpg")),
    //     componentStartName: 'image1',
    //     startEvent: ComponentEvent::END
    // );

    
    $tmp = __DIR__ . "/tmp/";
 
    $a->showFrame(1, $tmp, __DIR__ . "/output/test");
    //$a->generate($tmp, __DIR__ . "/output/test_2");
    //$a->showTime(3);

} catch( Throwable $e ) { 
    echo $e->getMessage() . "\n";
    echo $e->getFile() . " " . $e->getLine();
}