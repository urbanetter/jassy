<?php

namespace Jass\Ability;


use Jass\Ability;
use Jass\Entity\Player as PlayerEntity;
use Jass\Entity\Trick;
use function Jass\Hand\bock;
use Jass\Style;
use Jass\Hand;

class RecognisesAzeige implements Ability
{
    const SUIT_WANTED_BY_PARTNER = 'azeigt';
    const SUIT_WANTED_BY_ME = 'azoge';

    public static function seeTrick(PlayerEntity $player, Trick $trick, Style $style)
    {
        $playedCards = $player->brain[KnowsPlayedCards::CARDS] ?? [];

        // try to find out which suit the partner wants
        if ($trick->turns[2]->player === $player) {
            $leadingCard = $trick->turns[0]->card;
            if ($leadingCard !== Hand\bock($playedCards, $leadingCard->suit, $style->orderFunction())) {
                // no winner card, treat it like "azeigt"
                if (!isset($player->brain[self::SUIT_WANTED_BY_ME]) || $leadingCard->suit != $player->brain[self::SUIT_WANTED_BY_ME]) {
                    $player->brain[self::SUIT_WANTED_BY_PARTNER] = $leadingCard->suit;
                }
            }
        }

        // remember which suits you want
        if ($trick->turns[0]->player === $player) {
            $playedCard = $trick->turns[0]->card;

            if ((string)bock($playedCards, $playedCard->suit, $style->orderFunction()) != (string)$playedCard) {
                $player->brain[self::SUIT_WANTED_BY_ME] = $playedCard->suit;
            }
        }
    }
}