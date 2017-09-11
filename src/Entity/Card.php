<?php

namespace Jass\Entity;

use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;

class Card
{
    /**
     * @var string
     */
    public $suit;

    /**
     * @var string
     */
    public $value;

    public function __toString()
    {
        return $this->suit . " " . $this->value;
    }

    static function from($suit, $value)
    {
        $result = new Card();
        $result->suit = $suit;
        $result->value = $value;

        return $result;
    }

    static function shortcut($string)
    {
        $shortcutSuits = [
            'r' => Suit::ROSE,
            'b' => Suit::BELL,
            'o' => Suit::OAK,
            's' => Suit::SHIELD,
        ];

        $shortcutValues = [
            '6' => Value::SIX,
            '7' => Value::SEVEN,
            '8' => Value::EIGHT,
            '9' => Value::NINE,
            '10' => Value::TEN,
            'j' => Value::JACK,
            'q' => Value::QUEEN,
            'k' => Value::KING,
            'a' => Value::ACE
        ];

        $string = trim($string);
        $suit = strtolower(substr($string, 0, 1));
        $value = strtolower(substr($string, 1));

        if (!isset($shortcutSuits[$suit])) {
            throw new \InvalidArgumentException('Unknown suit shortcut: ' . $suit . ' in shortcuts: ' . $string);
        }
        $suit = $shortcutSuits[$suit];

        if (!isset($shortcutValues[$value])) {
            throw new \InvalidArgumentException('Unknown value shortcut: ' . $value . ' in shortcuts: ' . $string);
        }
        $value = $shortcutValues[$value];

        return self::from($suit, $value);
    }

}