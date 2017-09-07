<?php

namespace Jass\Style;


use Jass\Entity\Card;
use Jass\Entity\Card\Value;
use Jass\Entity\Team;
use Jass\Entity\Trick;
use Jass\Hand;
use function Jass\Trick\playedCards;
use function Jass\Trick\winner;

class TopDown extends Style
{

    /**
     * @param Card $card
     * @param string $leadingSuit
     * @return int
     */
    public function orderValue(Card $card, $leadingSuit = null)
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

    public function points(Card $card)
    {
        $values = [Value::EIGHT => 8, Value::TEN => 10, Value::JACK => 2, Value::QUEEN => 3, Value::KING => 4, Value::ACE => 11];

        return (isset($values[$card->value])) ? $values[$card->value] : 0;
    }

    public function teamPoints($tricks, $team)
    {
        $points = array_reduce($tricks, function ($points, Trick $trick) use ($team) {
            if (winner($trick, $this->orderFunction())->team == $team) {
                $points += array_sum(array_map(function (Card $card) {
                    return $this->points($card);
                }, playedCards($trick)));
            }
        }, 0);

        // winner of last trick in game gets 5 extra points
        $lastTrick  = Hand\last($tricks);
        if (winner($lastTrick, $this->orderFunction())->team == $team) {
            $points += 5;
        }

        // if matched, 100 extra points
        if ($points == 157) {
            $points += 100;
        }

        return $points;
    }

    public function name()
    {
        return "Obäabä";
    }

    public function isValidCard(Trick $trick, $hand, Card $card)
    {
        if ($trick->leadingSuit) {
            if (Hand\canFollowSuit($hand, $trick->leadingSuit)) {
                return $card->suit == $trick->leadingSuit;
            }
        } else {
            return true;
        }
    }
}