<?php

namespace Tests\Style;

use Jass\Entity\Card;
use Jass\Entity\Trick;
use Jass\Entity\Turn;
use function Jass\Player\byNames;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;

class TopDownTest extends TestCase
{
    public function testOrderValue()
    {
        $sut = new TopDown();

        $this->assertGreaterThan($sut->orderValue(Card::shortcut('b8')), $sut->orderValue(Card::shortcut('b9')));
        $this->assertGreaterThan($sut->orderValue(Card::shortcut('b10')), $sut->orderValue(Card::shortcut('bj')));
    }

    public function testPointValue()
    {
        $sut = new TopDown();

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
        $sut = new TopDown();

        $players = byNames('Ueli, Fritz, Susi, Frieda');
        $hand = [Card::from(Card\Suit::BELL, Card\Value::SIX), Card::from(Card\Suit::ROSE, Card\Value::ACE)];

        $trick = new Trick();
        $this->assertTrue($sut->isValidCard($trick, $hand, Card::shortcut('ra')));

        $trick->turns[] = new Turn($players[0], Card::shortcut('ba'));
        $trick->leadingSuit = Card\Suit::BELL;

        $this->assertFalse($sut->isValidCard($trick, $hand, Card::shortcut('ra')));


    }
}