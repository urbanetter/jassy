<?php

namespace Jass\Entity;

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

}