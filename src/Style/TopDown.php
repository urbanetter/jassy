<?php

namespace Jass\Style;


use Jass\Entity\Card;
use Jass\Entity\Card\Value;
use Jass\Entity\Trick;
use Jass\Hand;
use Jass\Style;

class TopDown extends Style
{

    public $name = "Obeabä";

    /**
     * @param Card $card
     * @param string $leadingSuit
     * @return int
     */
    public function orderValue(Card $card, $leadingSuit = null) : int
    {
        $order = $this->order();
        $result = array_search($card->value, $order);

        // increase order if its the same suit like leading turn
        if ($leadingSuit && $leadingSuit == $card->suit) {
            $result += 100;
        }
        return $result;
    }

    protected function order()
    {
        return [Value::SIX, Value::SEVEN, Value::EIGHT, Value::NINE, Value::TEN, Value::JACK, Value::QUEEN, Value::KING, Value::ACE];
    }

    public function points(Card $card) : int
    {
        $values = [Value::EIGHT => 8, Value::TEN => 10, Value::JACK => 2, Value::QUEEN => 3, Value::KING => 4, Value::ACE => 11];

        return (isset($values[$card->value])) ? $values[$card->value] : 0;
    }

    public function isValidCard(Trick $trick, $hand, Card $card) : bool
    {
        if ($trick->leadingSuit) {
            if (Hand\canFollowSuit($hand, $trick->leadingSuit)) {
                return $card->suit == $trick->leadingSuit;
            }
        }
        return true;
    }
}