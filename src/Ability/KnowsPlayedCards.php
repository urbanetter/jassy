<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Trick\playedCards;

class KnowsPlayedCards implements Ability
{

    public static function seeTrick(Player $player, Trick $trick)
    {
        $playedCards = $player->brain['playedCards'] ?? [];

        $playedCards = array_merge($playedCards, playedCards($trick));

        $player->brain['playedCards'] = $playedCards;
    }
}