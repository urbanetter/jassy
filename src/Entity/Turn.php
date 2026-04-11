<?php

namespace Jass\Entity;


class Turn
{
    public Player $player;
    public Card $card;

    public function __construct(Player $player, Card $card)
    {
        $this->player = $player;
        $this->card = $card;
    }
}