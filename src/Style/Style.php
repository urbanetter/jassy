<?php

namespace Jass\Style;


use Jass\Entity\Card;
use Jass\Entity\Team;
use Jass\Entity\Trick;

abstract class Style
{

    abstract public function orderValue(Card $card, $leadingSuit = null);

    public function orderFunction()
    {
        return [$this, "orderValue"];
    }

    abstract public function points(Card $card);

    public function pointFunction()
    {
        return [$this, "points"];
    }

    abstract public function teamPoints($tricks, $team);

    abstract public function isValidCard(Trick $trick, $hand, Card $card);

    abstract public function name();

    public function __toString()
    {
        return $this->name();
    }

}