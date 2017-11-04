<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use Jass\Entity\Card;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use function Jass\Strategy\card;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testSimpleStrategy()
    {
        $game = testGame([
            byShortcuts('sa,sq,r6,r7'),
            byShortcuts('ra,rk,s6,s7'),
            byShortcuts('r8,r9'),
        ]);

        $this->assertEquals(Card::shortcut('sa'), card($game));

        playCard($game, Card::shortcut('r6'));

        $this->assertEquals(Card::shortcut('ra'),card($game));

        playCard($game, Card::shortcut('ra'));

        $this->assertEquals(Card::shortcut('r8'), card($game));
    }
}