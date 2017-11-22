<?php

namespace Tests;


use function Jass\CardSet\byShortcuts;
use function Jass\Game\playCard;
use function Jass\Game\testGame;
use PHPUnit\Framework\TestCase;

class GameFunctionsTest extends TestCase
{
    public function testPlayCard()
    {
        $player1Cards = byShortcuts('sk,sq,o6,b6,b7,b8,b9,b10,bj');
        $player2Cards = byShortcuts('sa,ra,rk,rq,rj,r10,r9,r8,r7');

        $game = testGame([
            $player1Cards,
            $player2Cards,
            byShortcuts('o7'),
            byShortcuts('o8'),
        ]);

        $this->assertEquals($player1Cards, $game->currentPlayer->hand);
        $game = playCard($game, 'o6');

        $this->assertEquals($player2Cards, $game->currentPlayer->hand);

        $this->expectException(\LogicException::class);
        playCard($game, 'sk');

    }
}