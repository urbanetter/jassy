<?php

namespace Jass\Style;


class BottomUp extends TopDown
{
    public string $name = "bottom up";

    #[\Override]
    public function orderValue(\Jass\Entity\Card $card, ?\Jass\Entity\Card\Suit $leadingSuit = null) : int
    {
        $result = $card->value->bottomUpRank();

        if ($leadingSuit === $card->suit) {
            $result += 100;
        }

        return $result;
    }

    protected function order(): array
    {
        return array_reverse(parent::order());
    }

}
