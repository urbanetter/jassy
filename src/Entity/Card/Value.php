<?php

namespace Jass\Entity\Card;

enum Value: string
{
    case SIX = '6';
    case SEVEN = '7';
    case EIGHT = '8';
    case NINE = '9';
    case TEN = '10';
    case JACK = 'j';
    case QUEEN = 'q';
    case KING = 'k';
    case ACE = 'a';

    public function label() : string
    {
        return strtolower($this->name);
    }

    public static function fromLabel(string $label) : self
    {
        return constant(self::class . '::' . strtoupper($label));
    }

    public function topDownRank() : int
    {
        return match ($this) {
            self::SIX => 0,
            self::SEVEN => 1,
            self::EIGHT => 2,
            self::NINE => 3,
            self::TEN => 4,
            self::JACK => 5,
            self::QUEEN => 6,
            self::KING => 7,
            self::ACE => 8,
        };
    }

    public function bottomUpRank() : int
    {
        return 8 - $this->topDownRank();
    }

    public function trumpRank() : int
    {
        return match ($this) {
            self::SIX => 0,
            self::SEVEN => 1,
            self::EIGHT => 2,
            self::TEN => 3,
            self::QUEEN => 4,
            self::KING => 5,
            self::ACE => 6,
            self::NINE => 7,
            self::JACK => 8,
        };
    }

    public function topDownPoints() : int
    {
        return match ($this) {
            self::EIGHT => 8,
            self::TEN => 10,
            self::JACK => 2,
            self::QUEEN => 3,
            self::KING => 4,
            self::ACE => 11,
            default => 0,
        };
    }

    public function trumpPoints() : int
    {
        return match ($this) {
            self::JACK => 20,
            self::NINE => 14,
            self::TEN => 10,
            self::QUEEN => 3,
            self::KING => 4,
            self::ACE => 11,
            default => 0,
        };
    }
}
