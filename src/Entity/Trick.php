<?php

namespace Jass\Entity;


use Jass\Entity\Card\Suit;

class Trick
{
    /**
     * @var Turn[]
     */
    public array $turns = [];

    public ?Suit $leadingSuit = null;
}
