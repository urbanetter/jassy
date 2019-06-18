<?php

namespace Jass\Message;


use Jass\Entity\Card;
use Jass\Style;

class TestGame implements Message
{
    /** @var Style */
    public $style;

    /** @var  Card[]|Card[][] cards of first player or cards per player */
    public $cards;

    /** @var  string[] */
    public $strategies;

    /** @var  string[] */
    public $opponentStrategies;
}