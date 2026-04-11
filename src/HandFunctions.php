<?php

namespace Jass\Hand;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Style;
use LogicException;

/**
 * @param Card[] $hand
 * @param Suit $suit
 * @return Card[]
 */
function suit(array $hand, Suit $suit) : array
{
    return array_filter($hand, fn (Card $card) => $card->suit === $suit);
}

/**
 * @param Card[] $hand
 * @param Suit $suit
 * @return bool
 */
function canFollowSuit(array $hand, Suit $suit) : bool
{
    return array_any($hand, fn (Card $card) => $card->suit === $suit);
}

/**
 * @param Card[] $hand
 * @param Style $style
 * @return Card
 */
function lowest(array $hand, Style $style) : Card
{
    return array_reduce($hand, function($lowest, $card) use ($style) {
        if (!$lowest || $style->orderValue($card) < $style->orderValue($lowest)) {
            return $card;
        } else {
            return $lowest;
        }
    });
}

/**
 * @param Card[] $hand
 * @param Style $style
 * @return Card
 */
function highest(array $hand, Style $style) : Card
{
    return array_reduce($hand, function($highest, $card) use ($style) {
        if (!$highest || $style->orderValue($card) > $style->orderValue($highest)) {
            return $card;
        } else {
            return $highest;
        }
    });
}

/**
 * @param Card[] $hand
 * @param Style $style
 * @return Card[]
 */
function ordered(array $hand, Style $style) : array
{
    $suits = suits($hand);
    $result = [];
    foreach ($suits as $suit) {
        $cards = suit($hand, $suit);
        usort($cards, function ($a, $b) use ($style) {
            return $style->orderValue($a) <=> $style->orderValue($b);
        });
        $result = array_merge($result, $cards);
    }

    return $result;
}


function first($array)
{
    return is_array($array) ? array_first($array) : null;
}

function last($array)
{
    return is_array($array) ? array_last($array) : null;
}

/**
 * @param Card[] $hand
 * @return Suit[]
 */
function suits(array $hand) : array
{
    $suits = [];
    foreach ($hand as $card) {
        $suits[$card->suit->name] = $card->suit;
    }

    return array_values($suits);
}

/**
 * @param Card[] $hand
 * @param Card $played
 * @return Card[]
 */
function playCardOfHand(array $hand, Card $played) : array
{
    $result = [];
    foreach ($hand as $card) {
        if ((string) $card !== (string) $played) {
            $result[] = $card;
        }
    }

    if (count($hand) == count($result)) {
        throw new LogicException('Card ' . $played . ' is not in hand');
    }

    return $result;
}
