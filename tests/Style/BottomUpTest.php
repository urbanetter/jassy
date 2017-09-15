<?php

namespace Tests\Style;

use Jass\Entity\Card;
use Jass\Entity\Trick;
use function Jass\Player\byNames;
use Jass\Style\BottomUp;
use function Jass\Trick\addTurn;
use PHPUnit\Framework\TestCase;

class BottomUpTest extends TestCase
{
    public function testOrderValue()
    {
        $sut = new BottomUp();

        $this->assertGreaterThan($sut->orderValue(Card::shortcut('b9')), $sut->orderValue(Card::shortcut('b8')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('bj')), $sut->orderValue(Card::shortcut('b10')));
    }

    public function testPointValue()
    {
        $sut = new BottomUp();

        $this->assertEquals(0, $sut->points(Card::shortcut('b6')));
        $this->assertEquals(0, $sut->points(Card::shortcut('b7')));
        $this->assertEquals(8, $sut->points(Card::shortcut('s8')));
        $this->assertEquals(0, $sut->points(Card::shortcut('s9')));
        $this->assertEquals(10, $sut->points(Card::shortcut('s10')));
        $this->assertEquals(2, $sut->points(Card::shortcut('sj')));
        $this->assertEquals(3, $sut->points(Card::shortcut('sq')));
        $this->assertEquals(4, $sut->points(Card::shortcut('sk')));
        $this->assertEquals(11, $sut->points(Card::shortcut('sa')));
    }

    public function testIsValidCard()
    {
        $sut = new BottomUp();

        $players = byNames('Ueli, Fritz, Susi, Frieda');
        $hand = [Card::from(Card\Suit::BELL, Card\Value::SIX), Card::from(Card\Suit::ROSE, Card\Value::ACE)];

        $trick = new Trick();
        $this->assertTrue($sut->isValidCard($trick, $hand, Card::shortcut('ra')));

        addTurn($trick, $players[0], Card::shortcut('ba'));

        $this->assertFalse($sut->isValidCard($trick, $hand, Card::shortcut('ra')));
    }
}