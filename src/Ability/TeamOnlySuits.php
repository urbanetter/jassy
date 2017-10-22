<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Player\isInMyTeam;
use Jass\Style;

class TeamOnlySuits implements Ability
{
    const TEAM_ONLY_SUITS = 'hend nu mie';

    public static function seeTrick(Player $player, Trick $trick, Style $style)
    {
        $starter = $trick->turns[0]->player;
        if (isInMyTeam($player, $starter)) {
            if (
                $trick->turns[1]->card->suit != $trick->turns[0]->card->suit &&
                $trick->turns[3]->card->suit != $trick->turns[0]->card->suit
            ) {
                $player->brain[self::TEAM_ONLY_SUITS][] = $trick->turns[0]->card->suit;
            }
        }
    }
}