<?php

namespace Tests\Strategy;


use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use Jass\Entity\Turn;
use Jass\Strategy\Simple;
use Jass\Style\TopDown;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testSimpleStrategy()
    {
        $ueli = new Player();
        $fritz = new Player();
        $style = new TopDown();


        $ueli->hand = [
            Card::from(Card\Suit::ROSE, Card\Value::KING),
            Card::from(Card\Suit::ROSE, Card\Value::QUEEN),
            Card::from(Card\Suit::BELL, Card\Value::KING),
            Card::from(Card\Suit::BELL, Card\Value::JACK),
        ];

        $trick = new Trick;
        $trick->leadingSuit = Card\Suit::ROSE;

        $turn = new Turn();
        $turn->player = $fritz;
        $turn->card = Card::from(Card\Suit::ROSE, Card\Value::ACE);
        $trick->turns[] = $turn;

        $this->assertEquals(Card::from(Card\Suit::ROSE, Card\Value::QUEEN), Simple::card($ueli, $trick, $style));

        $trick = new Trick;
        $trick->leadingSuit = Card\Suit::BELL;

        $turn = new Turn();
        $turn->player = $fritz;
        $turn->card = Card::from(Card\Suit::BELL, Card\Value::QUEEN);
        $trick->turns[] = $turn;

        $this->assertEquals(Card::from(Card\Suit::BELL, Card\Value::KING), Simple::card($ueli, $trick, $style));

    }
}