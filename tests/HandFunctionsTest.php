<?php

namespace Tests;

use function Jass\CardSet\bySuitsAndValues;
use function Jass\CardSet\values;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use Jass\Entity\Card\Value;
use Jass\GameStyle\TopDown;
use function Jass\Hand\highest;
use function Jass\Hand\lowest;
use function Jass\Hand\potential;
use PHPUnit\Framework\TestCase;

class HandFunctionsTest extends TestCase
{
    public function testBock()
    {
        $style = new TopDown();

        $bock = \Jass\Hand\bock([], Suit::ROSE, $style->orderFunction());

        $this->assertNotNull($bock);
        $this->assertEquals(Card::from(Suit::ROSE, Value::ACE), $bock);

        $bock = \Jass\Hand\bock([Card::from(Suit::ROSE, Value::ACE)], Suit::ROSE, $style->orderFunction());
        $this->assertEquals(Card::from(Suit::ROSE, Value::KING), $bock);
    }

    public function testLowestHighest()
    {
        $style = new TopDown();

        $hand = bySuitsAndValues([Suit::BELL], values());

        $lowest = lowest($hand, $style->orderFunction());
        $this->assertEquals(Card::from(Suit::BELL, Value::SIX), $lowest);

        $highest = highest($hand, $style->orderFunction());
        $this->assertEquals(Card::from(Suit::BELL, Value::ACE), $highest);

    }

    public function testPotential()
    {
        $style = new TopDown();

        $handBell = bySuitsAndValues([Suit::BELL], [Value::KING, Value::QUEEN]);
        $handRose = bySuitsAndValues([Suit::ROSE], [Value::KING]);

        $potentialBell = potential([], $handBell, Suit::BELL, $style->orderFunction());
        $potentialRose = potential([], $handRose, Suit::ROSE, $style->orderFunction());

        $this->assertGreaterThan($potentialRose, $potentialBell);

        $playedCards = [Card::from(Suit::ROSE, Value::ACE)];
        $potentialBell = potential($playedCards, $handBell, Suit::BELL, $style->orderFunction());
        $potentialRose = potential($playedCards, $handRose, Suit::ROSE, $style->orderFunction());

        $this->assertGreaterThan($potentialBell, $potentialRose);

    }
}