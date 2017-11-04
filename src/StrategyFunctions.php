<?php

namespace Jass\Strategy;

use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Strategy;


function cardStrategy(Game $game) : Strategy
{
    foreach ($game->currentPlayer->strategies as $className) {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Unknown class ' . $className . ' of player ' . $game->currentPlayer);
        }
        $strategy = new $className();
        if (!$strategy instanceof Strategy) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not implement Jass\Strategy, but it must!');
        }

        if (!is_null($strategy->chooseCard($game))) {
            return $strategy;
        }
    }
    throw new \LogicException('Could not figure out next card for player ' . $game->currentPlayer);
}

function card(Game $game) : Card
{
    return cardStrategy($game)->chooseCard($game);
}
