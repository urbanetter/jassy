<?php

namespace Tests\Style;

use Jass\Entity\Card;
use Jass\Entity\Trick;
use function Jass\Player\byNames;
use Jass\Style\Trump;
use function Jass\Trick\addTurn;
use PHPUnit\Framework\TestCase;

class TrumpTest extends TestCase
{
    public function testOrderValue()
    {
        $sut = new Trump(Card\Suit::ROSE);

        // normal cards
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('b8')), $sut->orderValue(Card::shortcut('b9')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('b10')), $sut->orderValue(Card::shortcut('bj')));

        // trump beats other cards
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('ba')), $sut->orderValue(Card::shortcut('r6')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('sa')), $sut->orderValue(Card::shortcut('r6')));

        // trump cards
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('rk')), $sut->orderValue(Card::shortcut('ra')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('ra')), $sut->orderValue(Card::shortcut('r9')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('r9')), $sut->orderValue(Card::shortcut('rj')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('ra')), $sut->orderValue(Card::shortcut('rj')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('r8')), $sut->orderValue(Card::shortcut('rj')));


    }

    public function testPointValue()
    {
        $sut = new Trump(Card\Suit::ROSE);

        $this->assertEquals(0, $sut->points(Card::shortcut('r6')));
        $this->assertEquals(0, $sut->points(Card::shortcut('r7')));
        $this->assertEquals(0, $sut->points(Card::shortcut('r8')));
        $this->assertEquals(14, $sut->points(Card::shortcut('r9')));
        $this->assertEquals(10, $sut->points(Card::shortcut('r10')));
        $this->assertEquals(20, $sut->points(Card::shortcut('rj')));
        $this->assertEquals(3, $sut->points(Card::shortcut('sq')));
        $this->assertEquals(4, $sut->points(Card::shortcut('sk')));
        $this->assertEquals(11, $sut->points(Card::shortcut('sa')));
    }

    public function testIsValidCard()
    {
        $sut = new Trump(Card\Suit::ROSE);

        $players = byNames('Ueli, Fritz, Susi, Frieda');
        $hand = [Card::from(Card\Suit::BELL, Card\Value::SIX), Card::from(Card\Suit::ROSE, Card\Value::ACE)];

        $trick = new Trick();
        $this->assertTrue($sut->isValidCard($trick, $hand, Card::shortcut('ra')));

        addTurn($trick, $players[0], Card::shortcut('ba'));

        $this->assertTrue($sut->isValidCard($trick, $hand, Card::shortcut('ra')));

        addTurn($trick, $players[1], Card::shortcut('ra'));

        $this->assertFalse($sut->isValidCard($trick, $hand, Card::shortcut('rk')));
    }
}