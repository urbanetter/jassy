<?php

namespace Jass\Entity;


use Jass\Style\Style;

class Game
{
    const NUMBER_OF_CARDS = 9;
    const NUMBER_OF_PLAYERS = 4;

    /**
     * @var Player[]
     */
    public $players = [];

    /**
     * @var Style
     */
    public $style;

    /**
     * @var Trick[]
     */
    public $playedTricks = [];

    /**
     * @var Trick
     */
    public $currentTrick;

    /**
     * @var Player
     */
    public $currentPlayer;

}