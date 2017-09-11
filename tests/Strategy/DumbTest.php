<?php

namespace Tests\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Strategy\Dumb;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;

class DumbTest extends TestCase
{
    function testDumbStrategy()
    {
        $ueli = new Player();
        $style = new TopDown();

        $ueli->hand = [
            Card::from(Card\Suit::ROSE, Card\Value::ACE),
            Card::from(Card\Suit::ROSE, Card\Value::KING),
            Card::from(Card\Suit::BELL, Card\Value::QUEEN),
            Card::from(Card\Suit::BELL, Card\Value::JACK),
        ];

        $expected = Card::from(Card\Suit::ROSE, Card\Value::ACE);
        $this->assertEquals($expected, Dumb::firstCardOfTrick($ueli, $style));

        $trick = new Trick();
        $trick->leadingSuit = Card\Suit::ROSE;

        $expected = Card::from(Card\Suit::ROSE, Card\Value::ACE);
        $this->assertEquals($expected, Dumb::card($ueli, $trick, $style));

        $trick->leadingSuit = Card\Suit::OAK;
        $expected = Card::from(Card\Suit::BELL, Card\Value::JACK);
        $this->assertEquals($expected, Dumb::card($ueli, $trick, $style));

    }
}