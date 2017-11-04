<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style;

class TrickCounter implements Ability
{
    const TRICK_COUNTER = 'rundi';
    const CHOOSING_TEAM = 'het gseit';

    public static function seeTrick(Player $player, Trick $trick, Style $style)
    {
        $counter = $player->brain[self::TRICK_COUNTER] ?? 1;
        $counter++;
        $player->brain[self::TRICK_COUNTER] = $counter;
    }
}