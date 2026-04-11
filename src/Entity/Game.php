<?php

namespace Jass\Entity;


use Jass\Style;

class Game
{
    const NUMBER_OF_CARDS = 9;
    const NUMBER_OF_PLAYERS = 4;

    public ?string $name = null;

    /**
     * @var Player[]
     */
    public array $players = [];

    public ?Style $style = null;

    /**
     * @var Trick[]
     */
    public array $playedTricks = [];

    public ?Trick $currentTrick = null;

    public ?Player $currentPlayer = null;

}
