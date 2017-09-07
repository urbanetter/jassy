<?php

namespace Jass\Message;


use Jass\Entity\Card;

class Deal implements Message
{
    /**
     * @var Card[]
     */
    public $cards;
}