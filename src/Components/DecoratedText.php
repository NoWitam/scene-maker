<?php

namespace src\Components;

use src\Animations\Animation;
use src\Components\Enums\ComponentEvent;
use src\HtmlTags\HtmlTag;
use src\HtmlTags\OpenHtmlTag;
use src\Interfaces\Prepareable;

class DecoratedText extends Text implements Prepareable
{
    private array $decoratedStyles = [];
    private array $decoratedAnimations = [];
    private array $subTexts = [];

    public function prepare(array $data = []) : void
    {
        $words = explode(" ", $this->text);

        foreach ($words as $number => $word)
        {
            $number++;
            $styleAll = [];

            if(array_key_exists($number, $this->decoratedStyles)) {
                foreach($this->decoratedStyles[$number] as $style)
                {
                    $styleAll = array_merge($styleAll, $style);
                }
            } 

            $subText = SubText::make("")->styles($styleAll)
                                        ->text($word)
                                        ->setStart($this->getStart())
                                        ->setEnd($this->getEnd());

            if(array_key_exists($number, $this->decoratedAnimations)) {
                foreach($this->decoratedAnimations[$number] as $animationData)
                {
                    if(is_null($animationData['event'])) {
                        $subText->attachAnimation(
                            $animationData['animation']->setStart(
                                $this->getStart() + $animationData['animation']->getStart()
                            )->setEnd(
                                $this->getStart() + $animationData['animation']->getEnd()
                            )
                        );
                    } else {
                        $subText->attachAnimationRelative($animationData['animation'], $animationData['event']);
                    }
                }
            } 
            
            $this->subTexts[] = $subText;
        }
    }

    public function tag(float $time) : HtmlTag
    {
        return OpenHtmlTag::make(
            tag: 'div',
            content: OpenHtmlTag::make(
                tag: 'p',
                content: implode(" ", array_map(
                    function(SubText $subText) use ($time) {
                        return $subText->stroke($this->strokeSize, $this->strokeColor)->render($time);
                    },
                    $this->subTexts
                )),
            ),
            classes: ["textbox"],
            styles: $this->mergeStyles([
                'font-size' => $this->fontSize . "px",
                'text-align' => $this->align->value,
                'color' => $this->color,
                "align-items" => $this->verticalAlign->value
            ])
        );
    }

    public function pushDecoratedStyle(int|array $words, array $styles) : self
    {
        $wordsCount = count(
            explode(" ", $this->text)
        );

        if(is_int($words)) {
            $words = [$words];
        }

        foreach($words as $word)
        {
            if($word <= 0 OR $word > $wordsCount) {
                throw new \Exception("Component {$this->getName()} not have {$word} words");
            }

            if(array_key_exists($word, $this->decoratedStyles)) {
                $this->decoratedStyles[$word][] = $styles;
            } else {
                $this->decoratedStyles[$word] = [$styles];
            }
        }

        return $this;
    }

    public function pushDecoratedAnimation(int|array $words, Animation $animation, ?ComponentEvent $event = null) : self
    {
        $wordsCount = count(
            explode(" ", $this->text)
        );

        if(is_int($words)) {
            $words = [$words];
        }

        foreach($words as $word)
        {
            if($word <= 0 OR $word > $wordsCount) {
                throw new \Exception("Component {$this->getName()} not have {$word} words");
            }

            if(array_key_exists($word, $this->decoratedAnimations)) {
                $this->decoratedAnimations[$word][] = [
                    'animation' => $animation,
                    'event' => $event
                ];
            } else {
                $this->decoratedAnimations[$word] = [[
                    'animation' => $animation,
                    'event' => $event
                ]];
            }
        }

        return $this;
    }

}