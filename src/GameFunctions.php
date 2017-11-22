<?php

namespace Jass\Game;

use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\last;
use Jass\Message\TestGame;
use Jass\Message\Turn;
use Jass\MessageHandler;
use Jass\Style;
use function Jass\Trick\points;
use function Jass\Trick\winner;

function isFinished(Game $game) : bool
{
    return count($game->playedTricks) == Game::NUMBER_OF_CARDS;
}

function hasStarted(Game $game) : bool
{
    return count($game->playedTricks) > 0 || $game->currentTrick;
}

function isReady(Game $game) : bool
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

/**
 * @param Game $game
 * @return string[] team names
 */
function teams(Game $game)
{
    return [$game->players[0]->team, $game->players[1]->team];
}

function teamMatched(string $team, Game $game) : bool
{
    if (!isFinished($game)) {
        return false;
    }

    $tricksWon = array_filter($game->playedTricks, function(Trick $trick) use ($team, $game) {
        return winner($trick, $game->style->orderFunction())->team == $team;
    });

    return count($tricksWon) == Game::NUMBER_OF_CARDS;
}

function teamWonLastTrick(string $team, Game $game) : bool
{
    if (!isFinished($game)) {
        return false;
    }

    /** @var Trick $lastTrick */
    $lastTrick = last($game->playedTricks);
    return winner($lastTrick, $game->style->orderFunction())->team == $team;
}

function teamPoints(string $team, Game $game) : int
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

/**
 * @param Game $game
 * @return Card[]
 */
function startingHand(Game $game)
{
    $player = $game->currentPlayer;
    return array_reduce($game->playedTricks, function($cards, Trick $trick) use ($player) {
        foreach ($trick->turns as $turn) {
            if ($turn->player === $player) {
                $cards[] = $turn->card;
            }
        }
        return $cards;
    }, $player->hand);
}

function teamMate(Game $game, Player $player) : ?Player
{
    foreach ($game->players as $candidate) {
        if ($player !== $candidate && $candidate->team === $player->team) {
            return $candidate;
        }
    }
    return null;
}

function playCard(Game $game, $card) : Game
{
    if (is_string($card)) {
        $card = Card::shortcut($card);
    }

    $turn = new Turn();
    $turn->card = $card;

    $messageHandler = new MessageHandler();

    return $messageHandler->handle($game, $turn);
}

function testGame($cards = [], $style = null, $strategies = null, $opponentStrategies = null) : Game
{
    $game = new Game();

    $testGame = new TestGame();

    $testGame->cards = $cards;
    $testGame->style = $style;
    $testGame->strategies = $strategies;
    $testGame->opponentStrategies = $opponentStrategies;

    $messageHandler = new MessageHandler();

    return $messageHandler->handle($game, $testGame);
}