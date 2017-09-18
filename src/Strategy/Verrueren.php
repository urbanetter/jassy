<?php

namespace Jass\Strategy;


use Jass\Ability\KnowsPlayedCards;
use Jass\Ability\RecognisesVerrueren;
use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Strategy;
use Jass\Hand;
use Jass\Style\Style;

class Verrueren implements Strategy
{
    const BAD_SUITS = 'bad-suits';

    public static function firstCardOfTrick(Player $player, Style $style): ?Card
    {
        $verruert = $player->brain[RecognisesVerrueren::SUITS] ?? [];
        if (!$verruert) {
            return null;
        }

        $suits = Hand\suits($player->hand);

        $playable = array_diff($suits, $verruert);
        if (count($playable) == 1) {
            return Hand\lowest(Hand\suit($player->hand, Hand\first($playable)), $style->orderFunction());
        }

        return null;
    }

    public static function card(Player $player, Trick $trick, Style $style): ?Card
    {
        if (!isset($player->brain[self::BAD_SUITS])) {
            $player->brain[self::BAD_SUITS] = [];
            $suits = Hand\suits($player->hand);
            foreach ($suits as $suit) {
                $playedCards = $player->brain[KnowsPlayedCards::CARDS] ?? [];
                $potential = Hand\potential($playedCards, $player->hand, $suit, $style->orderFunction());
                if ($potential < 10) {
                    $player->brain[self::BAD_SUITS][$potential] = $suit;
                }
            }
            ksort($player->brain[self::BAD_SUITS]);
            $player->brain[self::BAD_SUITS] = array_values($player->brain[self::BAD_SUITS]);
        }
        if (!Hand\canFollowSuit($player->hand, $trick->leadingSuit)) {
            foreach ($player->brain[self::BAD_SUITS] as $badSuit) {
                $badCards = Hand\suit($player->hand, $badSuit);
                if ($badCards) {
                    return Hand\lowest($badCards, $style->orderFunction());
                }
            }
        }
        return null;
    }

    public static function abilities()
    {
        return [RecognisesVerrueren::class, KnowsPlayedCards::class];
    }
}