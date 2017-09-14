<?php

namespace Jass\Trick;

use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Entity\Turn;
use Jass\CardSet;

function isFinished(Trick $trick, $players)
{
    return count($trick->turns) == count($players);
}

function winner(Trick $trick, $valueFunction)
{
    return winningTurn($trick, $valueFunction)->player;
}

function winningTurn(Trick $trick, $valueFunction)
{
    $winningTurn = array_reduce($trick->turns, function ($winning, $turn) use ($valueFunction, $trick) {
        if (!$winning) {
            return $turn;
        }
        if ($valueFunction($turn->card, $trick->leadingSuit) > $valueFunction($winning->card, $trick->leadingSuit)) {
            return $turn;
        } else {
            return $winning;
        }
    });

    return $winningTurn;

}

function playedCards(Trick $trick)
{
    return ($trick->turns) ? array_map(function($turn) {
        return $turn->card;
    }, $trick->turns) : [];
}

function points(Trick $trick, $pointFunction)
{
    return array_reduce(\Jass\Trick\playedCards($trick), function($value, Card $card) use ($pointFunction) {
        return $value + $pointFunction($card);
    }, 0);
}

function addTurn(Trick $trick, Player $player, Card $card)
{
    if (count($trick->turns) == 4) {
        throw new \LogicException('There are already 4 turns for this trick');
    }
    $trick->turns[] = new Turn($player, $card);
    if (!$trick->leadingSuit) {
        $trick->leadingSuit = $card->suit;
    }
}

function byShortcuts($players, $cards)
{
    $result = new Trick();
    $cards = CardSet\byShortcuts($cards);
    foreach ($players as $player) {
        addTurn($result, $player, array_shift($cards));
    }
    return $result;
}
