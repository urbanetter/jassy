<?php

namespace Jass;


use Jass\Entity\Player;
use Jass\Entity\Trick;

interface Ability
{
    public static function seeTrick(Player $player, Trick $trick);
}