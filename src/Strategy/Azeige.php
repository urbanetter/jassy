<?php

namespace Jass\Strategy;


use Jass\Ability\KnowsPlayedCards;
use Jass\Entity\Card;
use Jass\Entity\Player as PlayerEntity;
use Jass\Entity\Trick as TrickEntity;
use Jass\Strategy;
use Jass\Style;
use Jass\Hand;
use Jass\Player;

class Azeige implements Strategy
{
    const SUIT_WANTED_BY_PARTNER = 'azeigt';

    public static function firstCardOfTrick(PlayerEntity $player, Style $style) : ?Card
    {
        if (
            isset($player->brain[self::SUIT_WANTED_BY_PARTNER]) &&
            Hand\canFollowSuit($player->hand, $player->brain[self::SUIT_WANTED_BY_PARTNER])
        ) {
            $card = Hand\highest(Hand\suit($player->hand, $player->brain[self::SUIT_WANTED_BY_PARTNER]), $style->orderFunction());
            unset($player->brain[self::SUIT_WANTED_BY_PARTNER]);
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
        $leadingTurn = $trick->turns[0];
        if (Player\isInMyTeam($player, $leadingTurn->player)) {
            $leadingCard = $leadingTurn->card;
            $playedCards = $player->brain[KnowsPlayedCards::CARDS] ?? [];
            if ($leadingCard !== Hand\bock($playedCards, $leadingCard->suit, $style->orderFunction())) {
                // no winner card, treat it like "azeigt"
                $player->brain[self::SUIT_WANTED_BY_PARTNER] = $leadingCard->suit;
            }
        }

        return null;
    }

    public static function abilities()
    {
        return [KnowsPlayedCards::class];
    }
}