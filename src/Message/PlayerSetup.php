<?php

namespace Jass\Message;

use Jass\Entity\Player;

class PlayerSetup implements Message
{
    /**
     * @var Player[]
     */
    public array $players = [];

    public ?Player $starter = null;
}
