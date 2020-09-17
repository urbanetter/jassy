<?php

namespace Jass;


use Jass\Entity\Card;
use Jass\Entity\Trick;

abstract class Style
{

    public $name;

    abstract public function orderValue(Card $card, $leadingSuit = null) : int;

    public function orderFunction()
    {
        return [$this, "orderValue"];
    }

    abstract public function points(Card $card) : int;

    public function pointFunction()
    {
        return [$this, "points"];
    }

    /**
     * @param Card[] $hand
     */
    abstract public function isValidCard(Trick $trick, array $hand, Card $card) : bool;

    public function __toString()
    {
        return $this->name;
    }

}