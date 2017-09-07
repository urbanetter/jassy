<?php

namespace Jass\Message;

use Jass\Entity\Player;

class PlayerSetup implements Message
{
    /**
     * @var Player[]
     */
    public $players;

    /**
     * @var Player
     */
    public $starter;
}