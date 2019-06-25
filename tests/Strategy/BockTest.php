<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use Jass\Strategy\Bock;
use PHPUnit\Framework\TestCase;

class BockTest extends TestCase
{
    public function testBock()
    {
        $game = testGame([
            byShortcuts('sk,sq,oj,b6,b7,b8,b9,b10,bj'),
            byShortcuts('sa,ra,rk,rq,rj,r10,r9,r8,r7'),
            byShortcuts('o7'),
            byShortcuts('o8'),
        ]);

        $sut = new Bock();
        $this->assertNull($sut->chooseCard($game));

        $game = playCard($game, 'oj');
        $game = playCard($game, 'sa');
        $game = playCard($game, 'o7');
        $game = playCard($game, 'o8');

        $this->assertEquals(Card::shortcut('sk'), $sut->chooseCard($game)->withoutHint());

    }
}