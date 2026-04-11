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
        $result = (int) array_search($card->value, $this->order(), true);

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
        return match ($card->value) {
            Value::EIGHT => 8,
            Value::TEN => 10,
            Value::JACK => 2,
            Value::QUEEN => 3,
            Value::KING => 4,
            Value::ACE => 11,
            default => 0,
        };
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
