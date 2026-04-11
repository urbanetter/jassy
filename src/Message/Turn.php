<?php

namespace Jass\Message;


use Jass\Entity\Card;

class Turn implements Message
{
    public Card $card;
}