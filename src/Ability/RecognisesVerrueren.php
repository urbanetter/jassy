<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player;
use Jass\Entity\Trick as TrickEntity;
use Jass\Style\Style;
use Jass\Trick;

class RecognisesVerrueren implements Ability
{
    const SUITS = 'verrüert';

    public static function seeTrick(Player $player, TrickEntity $trick, Style $style)
    {
        if (
            $trick->turns[0]->player === $player &&
            Trick\winner($trick, $style->orderFunction()) === $player &&
            $trick->turns[2]->card->suit != $trick->turns[0]->card->suit
        ) {
            $player->brain[self::SUITS] = $player->brain[self::SUITS] ?? [];
            $player->brain[self::SUITS][] = $trick->turns[2]->card->suit;
        }
    }
}