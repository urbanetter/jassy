<?php

namespace Tests;

use function Jass\CardSet\byShortcuts;
use function Jass\CardSet\bySuitsAndValues;
use function Jass\CardSet\values;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;
use function Jass\Hand\playCardOfHand;
use Jass\Style\TopDown;
use function Jass\Hand\highest;
use function Jass\Hand\lowest;
use PHPUnit\Framework\TestCase;

class HandFunctionsTest extends TestCase
{

    public function testLowestHighest()
    {
        $style = new TopDown();

        $hand = bySuitsAndValues([Suit::BELL], values());

        $lowest = lowest($hand, $style->orderFunction());
        $this->assertEquals(Card::from(Suit::BELL, Value::SIX), $lowest);

        $highest = highest($hand, $style->orderFunction());
        $this->assertEquals(Card::from(Suit::BELL, Value::ACE), $highest);
    }

    public function testPlayCardOfHand()
    {
        $hand = byShortcuts('sa, sq, sj');

        $expected = byShortcuts('sq, sj');
        $this->assertEquals($expected, playCardOfHand($hand, Card::shortcut('sa')));

        $this->expectException(\LogicException::class);
        playCardOfHand($hand, Card::shortcut('ra'));
    }
}