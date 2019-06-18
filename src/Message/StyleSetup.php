<?php

namespace Jass\Message;


use Jass\Style;

class StyleSetup implements Message
{
    /**
     * @var Style
     */
    public $style;

    /**
     * @var string[][] new strategies per player
     */
    public $strategies;
}