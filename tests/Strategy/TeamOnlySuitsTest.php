<?php

namespace Tests\Strategy;


use function Jass\CardSet\byShortcuts;
use function Jass\CardSet\bySuit;
use Jass\Entity\Card;
use Jass\Entity\Card\Suit;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use function Jass\Strategy\card;
use Jass\Strategy\TeamOnlySuits;
use PHPUnit\Framework\TestCase;

class TeamOnlySuitsTest extends TestCase
{
    public function testBasic()
    {
        $game = testGame([
            byShortcuts('sa,s6,b6,b7,b8,b9,b10,bj,bq'),
            bySuit(Suit::OAK),
            byShortcuts('ba,bk,s7,s8,s9,s10,sj,sq,sk'),
            bySuit(Suit::ROSE)
        ], null, [TeamOnlySuits::class]);

        $game = playCard($game, 'sa');
        $game = playCard($game, 'o6');
        $game = playCard($game, 's7');
        $game = playCard($game, 'r6');

        $actual = card($game);
        $this->assertEquals(Card::shortcut('s6'), $actual);

    }
}