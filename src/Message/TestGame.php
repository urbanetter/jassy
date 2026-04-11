<?php

namespace Jass\Message;


use Jass\Entity\Card;
use Jass\Style;

class TestGame implements Message
{
    public ?Style $style = null;

    /** @var  Card[]|Card[][] cards of first player or cards per player */
    public array $cards = [];

    /** @var  string[] */
    public ?array $strategies = null;

    /** @var  string[] */
    public ?array $opponentStrategies = null;
}
