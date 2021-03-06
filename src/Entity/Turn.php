<?php

namespace Jass\Entity;


class Turn
{


    /**
     * @var Player
     */
    public $player;

    /**
     * @var Card
     */
    public $card;

    /**
     * Turn constructor.
     * @param Player $player
     * @param Card $card
     */
    public function __construct(Player $player, Card $card)
    {
        $this->player = $player;
        $this->card = $card;
    }
}