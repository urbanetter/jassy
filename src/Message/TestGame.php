<?php

namespace Jass\Message;


use Jass\Entity\Card;
use Jass\Strategy;
use Jass\Style;

class TestGame implements Message
{
    /** @var Style */
    public $style;

    /** @var  Card[] */
    public $cards;

    /** @var  Strategy[] */
    public $strategies;

    /** @var  Strategy[] */
    public $opponentStrategies;
}