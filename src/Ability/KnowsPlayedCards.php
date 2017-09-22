<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style;
use function Jass\Trick\playedCards;

class KnowsPlayedCards implements Ability
{
    const CARDS = 'playedCards';

    public static function seeTrick(Player $player, Trick $trick, Style $style)
    {
        $playedCards = $player->brain[self::CARDS] ?? [];

        $playedCards = array_merge($playedCards, playedCards($trick));

        $player->brain[self::CARDS] = $playedCards;
    }
}