<?php

namespace Jass\Strategy;


use Jass\Ability\ClassifySuits;
use Jass\Ability\KnowsPlayedCards;
use Jass\Ability\RecognisesVerrueren;
use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Strategy;
use Jass\Hand;
use Jass\Style;

class Verrueren implements Strategy
{

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
        if (!Hand\canFollowSuit($player->hand, $trick->leadingSuit)) {
            $badSuits = $player->brain[ClassifySuits::BAD_SUITS] ?? [];
            foreach ($badSuits as $badSuit) {
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
        return [RecognisesVerrueren::class, KnowsPlayedCards::class, ClassifySuits::class];
    }
}