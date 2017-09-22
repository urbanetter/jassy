<?php

namespace Jass\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick as TrickEntity;
use Jass\Strategy;
use Jass\Style;
use Jass\Hand;
use Jass\Trick;

class Simple implements Strategy
{

    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        return Hand\highest($player->hand, $style->orderFunction());
    }

    public static function card(Player $player, TrickEntity $trick, Style $style): ?Card
    {
        if (Hand\canFollowSuit($player->hand, $trick->leadingSuit)) {
            $card = Hand\highest(Hand\suit($player->hand, $trick->leadingSuit), $style->orderFunction());
            $bestTrickCard = Hand\highest(Hand\suit(Trick\playedCards($trick), $trick->leadingSuit), $style->orderFunction());
            if ($style->orderValue($bestTrickCard) > $style->orderValue($card)) {
                $card =  Hand\lowest(Hand\suit($player->hand, $trick->leadingSuit), $style->orderFunction());
            }
        } else {
            $card = Hand\lowest($player->hand, $style->orderFunction());
        }

        return $card;

    }

    public static function abilities()
    {
        return [];
    }
}