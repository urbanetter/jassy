<?php

namespace Jass\Trick;

use Jass\Entity\Card;
use Jass\Entity\Game;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Entity\Turn;
use LogicException;

function isFinished(Trick $trick) : bool
{
    return count($trick->turns) == Game::NUMBER_OF_PLAYERS;
}

function winner(Trick $trick, Callable $orderFunction) : Player
{
    return winningTurn($trick, $orderFunction)->player;
}

function winningTurn(Trick $trick, Callable $orderFunction) : Turn
{
    return array_reduce($trick->turns, function ($winning, $turn) use ($orderFunction, $trick) {
        if (!$winning) {
            return $turn;
        }
        if ($orderFunction($turn->card, $trick->leadingSuit) > $orderFunction($winning->card, $trick->leadingSuit)) {
            return $turn;
        } else {
            return $winning;
        }
    });
}

function playerTurn(Trick $trick, Player $player) : ?Turn
{
    return array_find($trick->turns, fn (Turn $turn) => $turn->player === $player);
}

function leadingTurn(Trick $trick) : ?Turn
{
    return array_first($trick->turns);
}

/**
 * @param Trick $trick
 * @return Card[]
 */
function playedCards(Trick $trick) : array
{
    return ($trick->turns) ? array_map(fn(Turn $turn) => $turn->card, $trick->turns) : [];
}

function points(Trick $trick, Callable $pointFunction) : int
{
    return array_reduce(playedCards($trick), function($value, Card $card) use ($pointFunction) {
        return $value + $pointFunction($card);
    }, 0);
}

function addTurn(Trick $trick, Player $player, Card $card) : Trick
{
    if (count($trick->turns) == 4) {
        throw new LogicException('There are already 4 turns for this trick');
    }

    $trick->turns[] = new Turn($player, $card);
    if ($trick->leadingSuit === null) {
        $trick->leadingSuit = $card->suit;
    }

    return $trick;
}
