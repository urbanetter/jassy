<?php

namespace Jass\Hand;


use Jass\Entity\Card;

/**
 * @param Card[] $hand
 * @param string $suit
 * @return Card[]
 */
function suit($hand, $suit)
{
    return array_filter($hand, function(Card $card) use ($suit){
        return $card->suit == $suit;
    });
}

/**
 * @param Card[] $hand
 * @param string $suit
 * @return bool
 */
function canFollowSuit($hand, $suit)
{
    return count(suit($hand, $suit)) > 0;
}

/**
 * @param Card[] $hand
 * @param Callable $orderFunction
 * @return Card
 */
function lowest($hand, $orderFunction)
{
    return array_reduce($hand, function($lowest, $card) use ($orderFunction) {
        if (!$lowest || $orderFunction($card) < $orderFunction($lowest)) {
            return $card;
        } else {
            return $lowest;
        }
    });
}

/**
 * @param Card[] $hand
 * @param Callable $orderFunction
 * @return Card
 */
function highest($hand, Callable $orderFunction)
{
    return array_reduce($hand, function($highest, $card) use ($orderFunction) {
        if (!$highest || $orderFunction($card) > $orderFunction($highest)) {
            return $card;
        } else {
            return $highest;
        }
    });
}

/**
 * @param Card[] $hand
 * @param Callable $orderFunction
 * @return Card[]
 */
function ordered($hand, Callable $orderFunction)
{
    $suits = suits($hand);
    $result = [];
    foreach ($suits as $suit) {
        $cards = suit($hand, $suit);
        usort($cards, function ($a, $b) use ($orderFunction) {
            return $orderFunction($a) <=> $orderFunction($b);
        });
        $result = array_merge($result, $cards);
    }

    return $result;
}


function first($array)
{
    return ($array && is_array($array)) ? array_slice($array, 0, 1)[0] : null;
}

function last($array)
{
    return ($array && is_array($array)) ? array_slice($array, -1)[0] : null;
}

function suits($hand)
{
    $suits = array_map(function (Card $card) {
        return $card->suit;
    }, $hand);

    return array_unique($suits);
}

/**
 * @param Card[] $hand
 * @param Card $played
 * @return Card[]
 */
function playCardOfHand($hand, Card $played)
{
    $result = [];
    foreach ($hand as $card) {
        if ((string) $card != (string) $played) {
            $result[] = $card;
        }
    }

    if (count($hand) == count($result)) {
        throw new \LogicException('Card ' . $played . ' is not in hand');
    }

    return $result;
}
