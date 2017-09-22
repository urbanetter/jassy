<?php

namespace Tests\Strategy;

use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\highest;
use function Jass\Hand\suit;
use Jass\Strategy;
use Jass\Style;
use Tests\Ability\HelloWorld;

class RoseIsMyFavoriteSuit implements Strategy
{
    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        $roses = suit($player->hand, Suit::ROSE);
        if ($roses) {
            return highest($roses, $style->orderFunction());
        } else {
            return null;
        }
    }

    public static function card(Player $player, Trick $trick, Style $style): ?Card
    {
        return null;
    }

    public static function abilities()
    {
        return [HelloWorld::class];
    }
}