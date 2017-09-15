<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Strategy;
use Jass\Style\Style;
use Jass\CardSet;
use Jass\Hand;

class Bock implements Strategy
{

    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        $playedCards = $player->brain['playedCards'] ?? [];
        foreach (CardSet\suits() as $suit) {
            $bockCard = Hand\bock($playedCards, $suit, $style->orderFunction());
            if (in_array($bockCard, $player->hand)) {
                return $bockCard;
            }
        }

        return null;

    }

    public static function card(Player $player, Trick $trick, Style $style): ?Card
    {
        return null;
    }

    public static function abilities()
    {
        return ["KnowsPlayedCards"];
    }
}