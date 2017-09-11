<?php

namespace Jass\CardSet;


use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;

function jassSet()
{
    $suits = suits();
    $values = values();

    return bySuitsAndValues($suits, $values);
}

function bySuitsAndValues($suits, $values)
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

function suits()
{
    return [Suit::ROSE, Suit::BELL, Suit::OAK, Suit::SHIELD];
}

function values()
{
    return [Value::SIX, Value::SEVEN, Value::EIGHT, Value::NINE, Value::TEN, Value::JACK, Value::QUEEN, Value::KING, Value::ACE];
}

function isValidCard(Card $card)
{
    return (in_array($card->suit, suits()) && in_array($card->value, values()));
}

function byShortcuts($string)
{
    $cards = explode(",", $string);
    $result = [];
    foreach ($cards as $card) {
        $result[] = Card::shortcut($card);

    }
    return $result;
}
