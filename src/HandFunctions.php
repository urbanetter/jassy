<?php

namespace Jass\Hand;


use Jass\Entity\Card;
use Jass\CardSet;

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
function highest($hand, $orderFunction)
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
function ordered($hand, $orderFunction)
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

/**
 * @param Card[] $playedCards
 * @param string $suit
 * @param Callable $orderFunction
 * @return Card
 */
function bock($playedCards, $suit, $orderFunction)
{
    $playedSuit = suit($playedCards, $suit);
    $fullSuit = CardSet\bySuitsAndValues([$suit], CardSet\values());

    if ($playedSuit) {
        $unplayed = array_diff($fullSuit, $playedSuit);
    } else {
        $unplayed = $fullSuit;
    }

    return highest($unplayed, $orderFunction);
}

/**
 * @param Card[] $playedCards
 * @param Card[] $hand
 * @param string $suit
 * @param Callable $orderFunction
 * @return int
 */
function potential($playedCards, $hand, $suit, $orderFunction)
{
    $cards = suit($hand, $suit);

    if (!count($cards)) {
        return 0;
    }

    $neededCards = 0;

    $bestCard = bock($playedCards, $suit, $orderFunction);
    while ($bestCard && !in_array($bestCard, $cards) && $neededCards < count($cards)) {
        $playedCards[] = $bestCard;
        $neededCards++;
        $bestCard = bock($playedCards, $suit, $orderFunction);
    }

    $potential = 0;
    if ($neededCards < count($cards)) {
        $potential += (10 - $neededCards);
    }
    $potential = ($potential * 10) + count($cards);

    return $potential;

}

function bestSuit($playedCards, $hand, $orderFunction)
{
    $suits = suits($hand);
    $bestSuit = array_reduce($suits, function($best, $suit) use ($playedCards, $hand, $orderFunction) {
        if (!$best) {
            return $suit;
        }
        $suitScore = potential($playedCards, $hand, $suit, $orderFunction);
        if ($suitScore > potential($playedCards, $hand, $best, $orderFunction)) {
            return $suit;
        } else {
            return $best;
        }
    });

    return $bestSuit;
}

function worstSuit($playedCards, $hand, $orderFunction)
{
    $suits = suits($hand);
    $worstSuit = array_reduce($suits, function($worst, $suit) use ($playedCards, $hand, $orderFunction) {
        if (!$worst) {
            return $suit;
        }
        $suitScore = potential($playedCards, $hand, $suit, $orderFunction);
        if ($suitScore < potential($playedCards, $hand, $worst, $orderFunction)) {
            return $suit;
        } else {
            return $worst;
        }
    });

    return $worstSuit;
}

function first($array)
{
    return array_slice($array, 0, 1)[0];
}

function last($array)
{
    return array_slice($array, -1)[0];
}

function suits($hand)
{
    $suits = array_map(function (Card $card) {
        return $card->suit;
    }, $hand);

    return array_unique($suits);
}
