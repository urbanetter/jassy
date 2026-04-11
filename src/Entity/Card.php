<?php

namespace Jass\Entity;

use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;

readonly class Card
{
    public function __construct(
        public Suit $suit,
        public Value $value,
        public ?string $hint = null,
    ) {
    }

    public function withHint(string $hint) : Card
    {
        return clone($this, ['hint' => $hint]);
    }

    public function withoutHint() : Card
    {
        return clone($this, ['hint' => null]);
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
