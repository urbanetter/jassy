<?php

namespace Jass\Style;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;
use Jass\Entity\Trick;
use Jass\Hand;
use Jass\Style;

class TopDown extends Style
{

    public string $name = "top down";

    #[\Override]
    public function orderValue(Card $card, ?Suit $leadingSuit = null) : int
    {
        $result = $card->value->topDownRank();

        // increase order if its the same suit like leading turn
        if ($leadingSuit === $card->suit) {
            $result += 100;
        }
        return $result;
    }

    protected function order(): array
    {
        return [Value::SIX, Value::SEVEN, Value::EIGHT, Value::NINE, Value::TEN, Value::JACK, Value::QUEEN, Value::KING, Value::ACE];
    }

    #[\Override]
    public function points(Card $card) : int
    {
        return $card->value->topDownPoints();
    }

    #[\Override]
    public function isValidCard(Trick $trick, array $hand, Card $card) : bool
    {
        if (!in_array($card, $hand)) {
            return false;
        }
        if ($trick->leadingSuit !== null) {
            if (Hand\canFollowSuit($hand, $trick->leadingSuit)) {
                return $card->suit === $trick->leadingSuit;
            }
        }
        return true;
    }
}
