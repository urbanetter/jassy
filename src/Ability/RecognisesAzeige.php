<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player as PlayerEntity;
use Jass\Entity\Trick;
use Jass\Style;
use Jass\Hand;

class RecognisesAzeige implements Ability
{
    const SUIT_WANTED_BY_PARTNER = 'azeigt';

    public static function seeTrick(PlayerEntity $player, Trick $trick, Style $style)
    {
        if ($trick->turns[2]->player === $player) {
            $leadingCard = $trick->turns[0]->card;
            $playedCards = $player->brain[KnowsPlayedCards::CARDS] ?? [];
            if ($leadingCard !== Hand\bock($playedCards, $leadingCard->suit, $style->orderFunction())) {
                // no winner card, treat it like "azeigt"
                $player->brain[self::SUIT_WANTED_BY_PARTNER] = $leadingCard->suit;
            }
        }

    }
}