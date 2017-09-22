<?php

namespace Jass\Strategy;


use Jass\Ability\KnowsPlayedCards;
use Jass\Ability\RecognisesAzeige;
use Jass\Entity\Card;
use Jass\Entity\Player as PlayerEntity;
use Jass\Entity\Trick as TrickEntity;
use Jass\Strategy;
use Jass\Style;
use Jass\Hand;

class Azeige implements Strategy
{
    public static function firstCardOfTrick(PlayerEntity $player, Style $style) : ?Card
    {
        if (
            isset($player->brain[RecognisesAzeige::SUIT_WANTED_BY_PARTNER]) &&
            Hand\canFollowSuit($player->hand, $player->brain[RecognisesAzeige::SUIT_WANTED_BY_PARTNER])
        ) {
            $card = Hand\highest(Hand\suit($player->hand, $player->brain[RecognisesAzeige::SUIT_WANTED_BY_PARTNER]), $style->orderFunction());
            return $card;
        } else {
            // give a low card of your best suit to indicate to your team mate that she should play this suit
            $playedCards = $player->brain[KnowsPlayedCards::CARDS] ?? [];
            $bestSuit = Hand\bestSuit($playedCards, $player->hand, $style->orderFunction());
            return Hand\lowest(Hand\suit($player->hand, $bestSuit), $style->orderFunction());
        }
    }

    public static function card(PlayerEntity $player, TrickEntity $trick, Style $style) : ?Card
    {
        return null;
    }

    public static function abilities()
    {
        return [KnowsPlayedCards::class, RecognisesAzeige::class];
    }
}