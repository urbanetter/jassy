<?php

namespace Jass\Message;


use Jass\Entity\Card;

class Turn implements Message
{
    /**
     * @var Card
     */
    public $card;
}