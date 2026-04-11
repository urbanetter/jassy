<?php

namespace Jass\Message;


use Jass\Style;

class StyleSetup implements Message
{
    public ?Style $style = null;

    /**
     * @var string[][] new strategies per player
     */
    public array $strategies = [];
}
