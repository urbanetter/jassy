<?php

namespace Jass\Game;

use Jass\Entity\Game;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\last;
use Jass\Style\Style;
use function Jass\Trick\points;
use function Jass\Trick\winner;

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

function teams(Game $game)
{
    return [$game->players[0]->team, $game->players[1]->team];
}

function teamMatched(string $team, Game $game)
{
    if (!isFinished($game)) {
        return false;
    }

    $tricksWon = array_filter($game->playedTricks, function(Trick $trick) use ($team, $game) {
        return winner($trick, $game->style->orderFunction())->team == $team;
    });

    return $tricksWon == Game::NUMBER_OF_CARDS;
}

function teamWonLastTrick(string $team, Game $game)
{
    if (!isFinished($game)) {
        return false;
    }

    /** @var Trick $lastTrick */
    $lastTrick = last($game->playedTricks);
    return winner($lastTrick, $game->style->orderFunction())->team == $team;
}

function teamPoints(string $team, Game $game)
{
    $points = array_reduce($game->playedTricks, function($sum, Trick $trick) use ($game, $team) {
        if (winner($trick, $game->style->orderFunction())->team == $team) {
            $sum += points($trick, $game->style->pointFunction());
        }
        return $sum;
    }, 0);

    // winning last trick adds 5 points
    if (teamWonLastTrick($team, $game)) {
        $points += 5;
    }

    // winning all tricks adds 100 points
    if (teamMatched($team, $game)) {
        $points += 100;
    }

    return $points;
}