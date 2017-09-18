<?php

namespace Jass\Message;


use Jass\Style\Style;

class StyleSetup implements Message
{
    /**
     * @var Style
     */
    public $style;

    /**
     * @var string[] new strategies
     */
    public $strategies;
}