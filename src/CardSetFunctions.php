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
            $card = new Card();
            $card->suit = $suit;
            $card->value = $value;

            $cards[] = $card;
        }
    }

    return $cards;
}

/** @return string[] */
function suits() : array
{
    return [Suit::ROSE, Suit::BELL, Suit::OAK, Suit::SHIELD];
}

/**
 * @param string $suit
 * @return Card[]
 */
function bySuit(string $suit) : array
{
    return bySuitsAndValues([$suit], values());
}

/** @return string[] */
function values() : array
{
    return [Value::SIX, Value::SEVEN, Value::EIGHT, Value::NINE, Value::TEN, Value::JACK, Value::QUEEN, Value::KING, Value::ACE];
}

function isValidCard(Card $card) : bool
{
    return (in_array($card->suit, suits()) && in_array($card->value, values()));
}

/**
 * @param string $string
 * @return Card[]
 */
function byShortcuts(string $string) : array
{
    $cards = explode(",", $string);
    $result = [];
    foreach ($cards as $card) {
        $result[] = Card::shortcut($card);

    }
    return $result;
}
