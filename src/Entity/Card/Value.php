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
}
