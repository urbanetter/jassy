<?php

namespace Jass;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style\Style;

interface Strategy
{
    public static function firstCardOfTrick(Player $player, Style $style) : ?Card;

    public static function card(Player $player, Trick $trick, Style $style) : ?Card;

    public static function abilities();
}