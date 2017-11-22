<?php

namespace Jass;


use Jass\Entity\Game;

interface Knowledge
{
    static public function analyze(Game $game);
}