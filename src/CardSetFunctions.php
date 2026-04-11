<?php

namespace Jass\CardSet;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;

/** @return Card[] */
function jassSet() : array
{
    $suits = suits();
    $values = values();

    return bySuitsAndValues($suits, $values);
}

/**
 * @param array $suits
 * @param array $values
 * @return Card[]
 */
function bySuitsAndValues(array $suits, array $values) : array
{
    $cards = [];
    foreach ($suits as $suit) {
        foreach ($values as $value) {
            $cards[] = Card::from($suit, $value);
        }
    }

    return $cards;
}

/** @return Suit[] */
function suits() : array
{
    return Suit::cases();
}

/**
 * @param Suit $suit
 * @return Card[]
 */
function bySuit(Suit $suit) : array
{
    return bySuitsAndValues([$suit], values());
}

/** @return Value[] */
function values() : array
{
    return Value::cases();
}

/**
 * @param string $string
 * @return Card[]
 */
function byShortcuts(string $string) : array
{
    return array_map(
        fn (string $card) => Card::shortcut($card),
        explode(",", $string),
    );
}
