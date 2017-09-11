<?php

namespace Jass\Strategy;

use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Hand;
use Jass\Strategy;
use Jass\Style\Style;

class Dumb implements Strategy
{

    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        return Hand\highest($player->hand, $style->orderFunction());

    }

    public static function card(Player $player, Trick $trick, Style $style): ?Card
    {
        if (Hand\canFollowSuit($player->hand, $trick->leadingSuit)) {
            return Hand\highest(Hand\suit($player->hand, $trick->leadingSuit), $style->orderFunction());
        } else {
            return Hand\lowest($player->hand, $style->orderFunction());
        }
    }

    public static function abilities()
    {
        return [];
    }
}