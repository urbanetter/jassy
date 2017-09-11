<?php

namespace Tests\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;

class HelloWorld implements Ability
{

    public static function seeTrick(Player $player, Trick $trick)
    {
        $player->brain['hello'] = 'world';
    }
}