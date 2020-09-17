<?php

namespace Jass\Entity;

use InvalidArgumentException;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;

class Card
{
    const SHORTCUT_SUITS = [
        'r' => Suit::ROSE,
        'b' => Suit::BELL,
        'o' => Suit::OAK,
        's' => Suit::SHIELD,
    ];
    const SHORTCUT_VALUES = [
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


    /**
     * @var string
     */
    public $suit;

    /**
     * @var string
     */
    public $value;

    /**
     * @var string|null
     */
    public $hint;

    public function withHint(string $hint) : Card
    {
        $this->hint = $hint;
        return $this;
    }

    public function withoutHint() : Card
    {
        $this->hint = null;
        return $this;
    }

    public function __toString()
    {
        return $this->suit . " " . $this->value;
    }

    public function toShortcut() : string
    {
        return array_flip(self::SHORTCUT_SUITS)[$this->suit] . array_flip(self::SHORTCUT_VALUES)[$this->value];
    }

    static function from(string $suit, string $value) : Card
    {
        $result = new Card();
        $result->suit = $suit;
        $result->value = $value;

        return $result;
    }

    static function shortcut(string $string) : Card
    {
        $string = trim($string);
        $suit = strtolower(substr($string, 0, 1));
        $value = strtolower(substr($string, 1));

        if (!isset(self::SHORTCUT_SUITS[$suit])) {
            throw new InvalidArgumentException('Unknown suit shortcut: ' . $suit . ' in shortcuts: ' . $string);
        }
        $suit = self::SHORTCUT_SUITS[$suit];

        if (!isset(self::SHORTCUT_VALUES[$value])) {
            throw new InvalidArgumentException('Unknown value shortcut: ' . $value . ' in shortcuts: ' . $string);
        }
        $value = self::SHORTCUT_VALUES[$value];

        return self::from($suit, $value);
    }

}