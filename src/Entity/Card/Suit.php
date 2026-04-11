<?php

namespace Jass\Entity\Card;

enum Suit: string
{
    case OAK = "o";
    case SHIELD = "s";
    case BELL = "b";
    case ROSE = "r";

    public function label() : string
    {
        return strtolower($this->name);
    }

    public static function fromName(string $name) : self
    {
        return constant(self::class . '::' . $name);
    }

    public static function fromLabel(string $label) : self
    {
        return self::fromName(strtoupper($label));
    }
}
