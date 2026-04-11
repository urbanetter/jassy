<?php

namespace Jass;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Trick;

abstract class Style
{

    public string $name;

    abstract public function orderValue(Card $card, ?Suit $leadingSuit = null) : int;

    abstract public function points(Card $card) : int;

    /**
     * @param Card[] $hand
     */
    abstract public function isValidCard(Trick $trick, array $hand, Card $card) : bool;

    public function __toString() : string
    {
        return $this->name;
    }

}
