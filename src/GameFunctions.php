<?php

namespace Jass\Game;

use Jass\Entity\Game;
use Jass\Entity\Player;
use Jass\Style\Style;

function isFinished(Game $game)
{
    return count($game->playedTricks) == Game::NUMBER_OF_CARDS;
}

function hasStarted(Game $game)
{
    return count($game->playedTricks) > 0 || $game->currentTrick;
}

function isReady(Game $game)
{
    return
        $game->players
        && count($game->players) == Game::NUMBER_OF_PLAYERS
        && $game->players[3] instanceof Player
        && $game->style
        && $game->style instanceof Style
        && $game->currentPlayer
        && $game->currentPlayer instanceof Player
    ;
}