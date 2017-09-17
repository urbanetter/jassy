<?php

namespace Tests\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Style\Style;

class HelloWorld implements Ability
{

    public static function seeTrick(Player $player, Trick $trick, Style $style)
    {
        $player->brain['hello'] = 'world';
    }
}