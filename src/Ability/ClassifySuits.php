<?php

namespace Jass\Ability;

use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style;
use Jass\Hand;

class ClassifySuits implements Ability
{
    const BAD_SUITS = 'bad-suits';

    public static function seeTrick(Player $player, Trick $trick, Style $style)
    {
        if (!isset($player->brain[self::BAD_SUITS])) {
            $player->brain[self::BAD_SUITS] = [];
            $suits = Hand\suits($player->hand);
            foreach ($suits as $suit) {
                $playedCards = $player->brain[KnowsPlayedCards::CARDS] ?? [];
                $potential = Hand\potential($playedCards, $player->hand, $suit, $style->orderFunction());
                if ($potential < 10) {
                    $player->brain[self::BAD_SUITS][$potential] = $suit;
                }
            }
            ksort($player->brain[self::BAD_SUITS]);
            $player->brain[self::BAD_SUITS] = array_values($player->brain[self::BAD_SUITS]);
        }

    }
}