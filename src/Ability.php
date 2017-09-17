<?php

namespace Jass;


use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style\Style;

interface Ability
{
    public static function seeTrick(Player $player, Trick $trick, Style $style);
}