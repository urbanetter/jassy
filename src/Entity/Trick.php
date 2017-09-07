<?php

namespace Jass\Entity;


use Jass\Entity\Card\Suit;

class Trick
{
    /**
     * @var Turn[]
     */
    public $turns;

    /**
     * @var Suit
     */
    public $leadingSuit;
}