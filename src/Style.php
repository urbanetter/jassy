<?php

namespace Jass;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Trick;

abstract class Style
{

    public string $name;

    abstract public function orderValue(Card $card, ?Suit $leadingSuit = null) : int;

    public function orderFunction(): callable
    {
        return $this->orderValue(...);
    }

    abstract public function points(Card $card) : int;

    public function pointFunction(): callable
    {
        return $this->points(...);
    }

    /**
     * @param Card[] $hand
     */
    abstract public function isValidCard(Trick $trick, array $hand, Card $card) : bool;

    public function __toString() : string
    {
        return $this->name;
    }

}
