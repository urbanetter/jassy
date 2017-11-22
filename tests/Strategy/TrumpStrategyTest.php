<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Strategy\Bock;
use Jass\Strategy\Simple;
use Jass\Style\Trump;
use PHPUnit\Framework\TestCase;

class TrumpStrategyTest extends TestCase
{
    public function testTrump()
    {
        $game = testGame(
            [
                byShortcuts('rj,r9,ra,ba,bk,o6,o7,o8,s6'),
                byShortcuts('rq,r10,oa,ok,o9,s7,s8,s9,s10'),
                byShortcuts('r6,r7,r8,oq,oj,sa,sj,b6,b7'),
                [] // all the other cards
            ],
            new Trump(Suit::ROSE)
        );

        $strategy = new \Jass\Strategy\Trump();
        $simple = new Simple();
        $bock = new Bock();

        $this->assertEquals($strategy->chooseCard($game), Card::shortcut('rj'));
        playCard($game, 'rj');
        playCard($game, 'r10');
        playCard($game, 'r6');
        playCard($game, $simple->chooseCard($game));

        $this->assertEquals($strategy->chooseCard($game), Card::shortcut('r9'));
        playCard($game, 'r9');
        playCard($game, 'rq');
        playCard($game, 'r7');
        playCard($game, $simple->chooseCard($game));

        $this->assertNull($strategy->chooseCard($game));
        $this->assertNotEquals(Card::shortcut('ra'), $bock->chooseCard($game));

    }
}