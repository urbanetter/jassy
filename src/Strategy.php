<?php

namespace Jass;

use Jass\Entity\Card;
use Jass\Entity\Game;

interface Strategy
{
    public function chooseCard(Game $game) : ?Card;
}
