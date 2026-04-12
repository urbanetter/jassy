<?php

namespace Jass\Entity;

use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;

class Card
{
    public function __construct(
        public readonly Suit $suit,
        public readonly Value $value,
        public readonly ?string $hint = null,
    ) {
    }

    public function withHint(string $hint) : Card
    {
        return new Card($this->suit, $this->value, $hint);
    }

    public function withoutHint() : Card
    {
        return new Card($this->suit, $this->value);
    }

    public function __toString() : string
    {
        return $this->suit->label() . " " . $this->value->label();
    }

    public function toShortcut() : string
    {
        return $this->suit->value . $this->value->value;
    }

    public function __serialize() : array
    {
        return [
            'suit' => $this->suit,
            'value' => $this->value,
            'hint' => $this->hint,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->suit = $data['suit'] instanceof Suit ? $data['suit'] : Suit::fromLabel($data['suit']);
        $this->value = $data['value'] instanceof Value ? $data['value'] : Value::fromLabel($data['value']);
        $this->hint = $data['hint'] ?? null;
    }

    static function from(Suit $suit, Value $value) : Card
    {
        return new Card($suit, $value);
    }

    static function shortcut(string $string) : Card
    {
        $string = trim($string);
        $suit = Suit::from(strtolower(substr($string, 0, 1)));
        $value = Value::from(strtolower(substr($string, 1)));

        return self::from($suit, $value);
    }

}
