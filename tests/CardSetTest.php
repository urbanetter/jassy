<?php

namespace Tests;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use PHPUnit\Framework\TestCase;

class CardSetTest extends TestCase
{
    public function testShortcut()
    {
        $expected = [
            Card::from(Card\Suit::ROSE, Card\Value::ACE),
            Card::from(Card\Suit::ROSE, Card\Value::KING),
            Card::from(Card\Suit::ROSE, Card\Value::QUEEN),
            Card::from(Card\Suit::BELL, Card\Value::KING),
            Card::from(Card\Suit::BELL, Card\Value::JACK),
            Card::from(Card\Suit::OAK, Card\Value::SEVEN),
            Card::from(Card\Suit::SHIELD, Card\Value::TEN),
            Card::from(Card\Suit::SHIELD, Card\Value::ACE),
        ];

        $cards = byShortcuts("ra, rk, rq, bk, bj, o7, s10, sa");

        $this->assertEquals($expected, $cards);
    }

    public function testFailure()
    {
        $this->expectException(\InvalidArgumentException::class);
        Card::shortcut('wrong');
    }
}