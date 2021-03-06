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

        $this->assertEquals($strategy->chooseCard($game)->withoutHint(), Card::shortcut('rj'));
        playCard($game, 'rj');
        playCard($game, 'r10');
        playCard($game, 'r6');
        playCard($game, $simple->chooseCard($game)->withoutHint());

        $this->assertEquals($strategy->chooseCard($game)->withoutHint(), Card::shortcut('r9'));
        playCard($game, 'r9');
        playCard($game, 'rq');
        playCard($game, 'r7');
        playCard($game, $simple->chooseCard($game)->withoutHint());

        $this->assertNull($strategy->chooseCard($game));
        $this->assertNotEquals(Card::shortcut('ra'), $bock->chooseCard($game)->withoutHint());

    }

    public function testOvertrumpsIfLowerThanAceOnFirstTrick()
    {
        $game = testGame(
            [
                byShortcuts('sa,r7,r8,rk,oj,sa,sj,b6,b7'),
                byShortcuts('oq,o10,oa,ok,o9,s7,s8,s9,s10'),
                byShortcuts('rj,r9,ra,ba,bk,o6,o7,o8,s6'),
                [] // all the other cards
            ],
            new Trump(Suit::ROSE)
        );

        $strategy = new \Jass\Strategy\Trump();

        $this->assertEquals($strategy->chooseCard($game)->withoutHint(), Card::shortcut("rk"));
        playCard($game, 'rk');
        playCard($game, 's7');
        $this->assertEquals($strategy->chooseCard($game)->withoutHint(), Card::shortcut("rj"));

        $game = testGame(
            [
                byShortcuts('ra,r7,r8,rk,oj,sa,sj,b6,b7'),
                byShortcuts('oq,o10,oa,ok,o9,s7,s8,s9,s10'),
                byShortcuts('rj,r9,sa,ba,bk,o6,o7,o8,s6'),
                [] // all the other cards
            ],
            new Trump(Suit::ROSE)
        );

        $this->assertEquals($strategy->chooseCard($game)->withoutHint(), Card::shortcut("ra"));
        playCard($game, 'ra');
        playCard($game, 's7');
        $this->assertNull($strategy->chooseCard($game));

    }
}