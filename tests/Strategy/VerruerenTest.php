<?php

namespace Tests\Strategy;


use Jass\Ability\ClassifySuits;
use Jass\Ability\RecognisesVerrueren;
use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Player;
use Jass\Entity\Trick;
use function Jass\Hand\playCardOfHand;
use Jass\Strategy\Verrueren;
use Jass\Style\TopDown;
use function Jass\Trick\addTurn;
use PHPUnit\Framework\TestCase;

class VerruerenTest extends TestCase
{
    public function testNullIfNotRecognised()
    {
        $player = new Player();
        $style = new TopDown();

        $this->assertNull(Verrueren::firstCardOfTrick($player, $style));
    }

    public function testFirstCardIfRecognised()
    {
        $player = new Player();
        $style = new TopDown();

        $player->hand = byShortcuts('s6, r6');
        $player->brain[RecognisesVerrueren::SUITS] = [Card\Suit::SHIELD];

        $this->assertEquals(Card::shortcut('r6'), Verrueren::firstCardOfTrick($player, $style));

        $player->brain[RecognisesVerrueren::SUITS][] = Card\Suit::ROSE;
        $this->assertNull(Verrueren::firstCardOfTrick($player, $style));
    }

    public function testOnlyIfWeHaveOneSuit()
    {
        $player = new Player();
        $style = new TopDown();

        $player->hand = byShortcuts('s6, r6, b6');
        $player->brain[RecognisesVerrueren::SUITS] = [Card\Suit::SHIELD];

        $this->assertNull(Verrueren::firstCardOfTrick($player, $style));

    }

    public function testConsequentlyVerrueren()
    {
        $player = new Player();
        $starter = new Player();
        $style = new TopDown();

        $player->hand = byShortcuts('sq, sj, bj, rk, rq, r6');
        $trick = new Trick();

        ClassifySuits::seeTrick($player, $trick, $style);

        $trick = addTurn($trick, $starter, Card::shortcut('oa'));

        $actual = Verrueren::card($player, $trick, $style);
        $this->assertEquals(Card::shortcut('bj'), $actual);

        $player->hand = playCardOfHand($player->hand, Card::shortcut('bj'));
        $this->assertEquals(Card::shortcut('sj'), Verrueren::card($player, $trick, $style));
        $player->hand = playCardOfHand($player->hand, Card::shortcut('sj'));

        $this->assertEquals(Card::shortcut('sq'), Verrueren::card($player, $trick, $style));
        $player->hand = playCardOfHand($player->hand, Card::shortcut('sq'));

        $this->assertNull(Verrueren::card($player, $trick, $style));

    }

}